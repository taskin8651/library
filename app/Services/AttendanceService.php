<?php
namespace App\Services;

use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Checks a member in if they aren't currently inside, or checks them out
     * if they already have an open attendance record for today. Shared by
     * the public QR check-in page and the logged-in student's camera scanner.
     */
    public function toggle(Member $member): array
    {
        if ($member->status !== 'active' || $member->isExpired()) {
            return ['success' => false, 'message' => 'Your plan has expired. Please renew.'];
        }

        $today = Carbon::today();
        $existing = Attendance::where('member_id', $member->id)->whereDate('date', $today)->whereNull('check_out')->first();

        if ($existing) {
            $existing->update(['check_out' => now()]);
            return [
                'success' => true,
                'action'  => 'checkout',
                'message' => 'Checked out successfully!',
                'member'  => $member->user->name,
            ];
        }

        Attendance::create([
            'library_id' => $member->library_id,
            'member_id'  => $member->id,
            'seat_id'    => $member->seat_id,
            'check_in'   => now(),
            'date'       => $today,
        ]);

        return [
            'success'   => true,
            'action'    => 'checkin',
            'message'   => 'Checked in successfully!',
            'member'    => $member->user->name,
            'seat'      => $member->seat?->seat_number,
            'days_left' => $member->daysLeft(),
        ];
    }
}
