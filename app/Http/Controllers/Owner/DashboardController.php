<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\FeePayment;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $library = Auth::user()->library;
        $today = Carbon::today();

        $data = [
            'library'         => $library,
            'total_members'   => Member::where('library_id',$library->id)->count(),
            'active_members'  => Member::where('library_id',$library->id)->where('status','active')->count(),
            'expiring_soon'   => Member::where('library_id',$library->id)
                                    ->where('status','active')
                                    ->whereBetween('plan_end_date',[$today, $today->copy()->addDays(7)])
                                    ->count(),
            'today_revenue'   => FeePayment::where('library_id',$library->id)
                                    ->whereDate('payment_date',$today)->sum('amount'),
            'monthly_revenue' => FeePayment::where('library_id',$library->id)
                                    ->whereMonth('payment_date',$today->month)
                                    ->whereYear('payment_date',$today->year)->sum('amount'),
            'currently_in'    => Attendance::where('library_id',$library->id)
                                    ->whereDate('date',$today)->whereNull('check_out')->count(),
            'today_visits'    => Attendance::where('library_id',$library->id)
                                    ->whereDate('date',$today)->count(),
            'recent_payments' => FeePayment::with('member.user')
                                    ->where('library_id',$library->id)->latest()->take(5)->get(),
            'active_sessions' => Attendance::with('member.user','seat')
                                    ->where('library_id',$library->id)
                                    ->whereDate('date',$today)->whereNull('check_out')->get(),
        ];

        return view('owner.dashboard', $data);
    }
}
