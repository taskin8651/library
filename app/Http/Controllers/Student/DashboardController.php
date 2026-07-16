<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\FeePayment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $member  = $user->member;
        $library = $user->library;

        if (!$member) return view('student.no_member', compact('user','library'));

        $member->load('seat','shift','feePayments');

        // Which audience segments this member currently belongs to.
        $memberAudiences = ['all'];
        if ($member->status === 'active') $memberAudiences[] = 'active';
        if ($member->plan_end_date && $member->daysLeft() <= 7) $memberAudiences[] = 'expiring';

        $data = [
            'member'      => $member,
            'library'     => $library,
            'days_left'   => $member->daysLeft(),
            'today_in'    => Attendance::where('member_id',$member->id)->whereDate('date',today())->whereNotNull('check_in')->exists(),
            'this_month'  => Attendance::where('member_id',$member->id)->whereMonth('date',now()->month)->count(),
            'last_payment'=> FeePayment::where('member_id',$member->id)->latest()->first(),
            'recent_attendance' => Attendance::where('member_id',$member->id)->latest()->take(10)->get(),
            'announcements' => Announcement::where('library_id',$library->id)
                ->where('is_active',true)
                ->whereIn('target_audience',$memberAudiences)
                ->where(fn($q) => $q->whereNull('scheduled_at')->orWhere('scheduled_at','<=',now()))
                ->latest()->take(5)->get(),
        ];

        return view('student.dashboard', $data);
    }
}
