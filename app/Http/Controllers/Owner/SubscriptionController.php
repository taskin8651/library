<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    private function library() { return Auth::user()->library; }

    private function razorpay(): Api
    {
        return new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function plans()
    {
        $plans   = Plan::where('is_active',true)->get();
        $library = $this->library();
        return view('owner.subscription.plans', compact('plans','library'));
    }

    public function createOrder(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $library = $this->library();
        $plan    = Plan::findOrFail($request->plan_id);

        $api = $this->razorpay();
        $order = $api->order->create([
            'amount'   => $plan->price * 100, // in paise
            'currency' => 'INR',
            'receipt'  => 'LIB-' . $library->id . '-' . time(),
            'notes'    => [
                'library_id' => $library->id,
                'plan_id'    => $plan->id,
                'plan_name'  => $plan->name,
            ],
        ]);

        // Create pending subscription
        Subscription::create([
            'library_id'        => $library->id,
            'plan_id'           => $plan->id,
            'razorpay_order_id' => $order->id,
            'amount'            => $plan->price,
            'status'            => 'pending',
        ]);

        return response()->json([
            'order_id'   => $order->id,
            'amount'     => $plan->price * 100,
            'currency'   => 'INR',
            'plan_name'  => $plan->name,
            'rzp_key'    => config('services.razorpay.key'),
            'library'    => $library->name,
            'email'      => $library->email,
            'phone'      => $library->phone,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature'  => 'required',
        ]);

        $api = $this->razorpay();

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $subscription = Subscription::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
            $library      = $this->library();

            $expiresAt = Carbon::now()->addDays(30);
            $subscription->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status'     => 'active',
                'starts_at'  => now(),
                'expires_at' => $expiresAt,
            ]);

            $library->update([
                'plan_id'         => $subscription->plan_id,
                'status'          => 'active',
                'plan_expires_at' => $expiresAt,
            ]);

            return redirect('/owner/dashboard')->with('success', 'Payment successful! Your plan is now active till ' . $expiresAt->format('d M Y'));

        } catch (\Exception $e) {
            return redirect('/owner/subscription/plans')->with('error', 'Payment verification failed. Please contact support.');
        }
    }
}
