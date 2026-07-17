<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

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
}
