<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class SubscriptionController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function plans()
    {
        $plans   = Plan::where('is_active',true)->get();
        $library = $this->library();
        return view('owner.subscription.plans', compact('plans','library'));
    }

    // Creates a pending subscription and returns a UPI QR code + payment
    // link for the owner to pay manually (no payment gateway configured yet).
    public function createOrder(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $library = $this->library();
        $plan    = Plan::findOrFail($request->plan_id);

        $subscription = Subscription::create([
            'library_id'     => $library->id,
            'plan_id'        => $plan->id,
            'payment_method' => 'upi',
            'amount'         => $plan->price,
            'status'         => 'pending',
        ]);

        $upiId   = config('services.upi.id');
        $payee   = config('services.upi.name');
        $note    = $library->name . ' - ' . $plan->name . ' Plan';
        $upiLink = 'upi://pay?' . http_build_query([
            'pa' => $upiId,
            'pn' => $payee,
            'am' => number_format($plan->price, 2, '.', ''),
            'cu' => 'INR',
            'tn' => $note,
            'tr' => 'SUB' . $subscription->id,
        ]);

        $qr = (string) QrCode::format('svg')->size(220)->generate($upiLink);

        return response()->json([
            'subscription_id' => $subscription->id,
            'upi_id'           => $upiId,
            'upi_link'         => $upiLink,
            'upi_qr'           => $qr,
            'amount'           => $plan->price,
            'plan_name'        => $plan->name,
        ]);
    }

    // Owner submits their UPI transaction reference (UTR) after paying.
    // The subscription stays "pending" until an admin verifies the payment
    // actually landed and approves it from the admin panel.
    public function submitUtr(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'utr'              => 'required|string|max:50',
        ]);

        $library = $this->library();
        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('library_id', $library->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $subscription->update(['utr' => $request->utr]);

        return response()->json([
            'success' => true,
            'message' => 'Payment submitted! We will verify it and activate your plan shortly.',
        ]);
    }

    // Creates a pending subscription + a matching Razorpay order. The
    // frontend opens Razorpay Checkout with this order_id; payment is
    // confirmed server-side in verifyRazorpayPayment() below (never trust
    // a client-side "success" callback alone for activating a paid plan).
    public function createRazorpayOrder(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $library = $this->library();
        $plan    = Plan::findOrFail($request->plan_id);

        $subscription = Subscription::create([
            'library_id'     => $library->id,
            'plan_id'        => $plan->id,
            'payment_method' => 'razorpay',
            'amount'         => $plan->price,
            'status'         => 'pending',
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $order = $api->order->create([
                'receipt'  => 'SUB' . $subscription->id,
                'amount'   => (int) round($plan->price * 100), // paise
                'currency' => 'INR',
                'notes'    => [
                    'library_id'      => $library->id,
                    'subscription_id' => $subscription->id,
                    'plan_name'       => $plan->name,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            $subscription->update(['status' => 'failed']);
            return response()->json(['error' => 'Could not start payment. Please try again.'], 422);
        }

        $subscription->update(['razorpay_order_id' => $order->id]);

        return response()->json([
            'subscription_id' => $subscription->id,
            'order_id'         => $order->id,
            'amount'           => $plan->price,
            'plan_name'        => $plan->name,
            'razorpay_key'     => config('services.razorpay.key'),
            'name'             => $library->name,
            'email'            => $library->email,
            'phone'            => $library->phone,
        ]);
    }

    // Verifies the Razorpay payment signature server-side and, only if
    // valid, activates the subscription. This is the step that actually
    // grants the plan — the Checkout "handler" callback on its own is not
    // trusted since it runs entirely in the browser.
    public function verifyRazorpayPayment(Request $request, SubscriptionActivationService $activation)
    {
        $request->validate([
            'subscription_id'    => 'required|exists:subscriptions,id',
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $library = $this->library();
        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('library_id', $library->id)
            ->where('status', 'pending')
            ->where('razorpay_order_id', $request->razorpay_order_id)
            ->firstOrFail();

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
        } catch (SignatureVerificationError $e) {
            $subscription->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 422);
        }

        $subscription->update(['razorpay_payment_id' => $request->razorpay_payment_id]);
        $activation->activate($subscription);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful! Your ' . $subscription->plan->name . ' plan is now active.',
        ]);
    }
}
