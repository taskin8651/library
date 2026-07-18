<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionActivationService
{
    /**
     * Marks a pending subscription active and rolls the library onto
     * that plan for 30 days. Shared by the admin's manual UPI approval
     * and the automatic Razorpay payment verification so both payment
     * paths activate a plan identically.
     */
    public function activate(Subscription $subscription): void
    {
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
    }
}
