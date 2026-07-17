<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Carbon\Carbon;

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

    public function approve(Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $expiresAt = Carbon::now()->addDays(30);
        $subscription->update([
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => $expiresAt,
        ]);

        $subscription->library->update([
            'plan_id'         => $subscription->plan_id,
            'status'          => 'active',
            'plan_expires_at' => $expiresAt,
        ]);

        return back()->with('success', 'Payment approved — ' . $subscription->library->name . '\'s plan is active till ' . $expiresAt->format('d M Y') . '.');
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
