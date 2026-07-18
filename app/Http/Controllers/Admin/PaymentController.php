<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionActivationService;

class PaymentController extends Controller
{
    public function index()
    {
        $awaitingVerification = Subscription::with('library', 'plan')
            ->where('status', 'pending')->whereNotNull('utr')
            ->latest()->get();

        $history = Subscription::with('library', 'plan')
            ->whereIn('status', ['active', 'failed', 'cancelled'])
            ->latest()->take(30)->get();

        return view('admin.payments.index', compact('awaitingVerification', 'history'));
    }

    public function approve(Subscription $subscription, SubscriptionActivationService $activation)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $activation->activate($subscription);

        return back()->with('success', 'Payment approved — ' . $subscription->library->name . '\'s plan is active till ' . $subscription->expires_at->format('d M Y') . '.');
    }

    public function reject(Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $subscription->update(['status' => 'failed']);

        return back()->with('success', 'Payment marked as failed.');
    }
}
