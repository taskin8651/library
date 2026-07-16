<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index(Request $request)
    {
        $library = $this->library();
        $today = Carbon::today();

        $attendance = Attendance::with('member.user','seat')
            ->where('library_id',$library->id)
            ->when($request->date, fn($q) => $q->whereDate('date',$request->date), fn($q) => $q->whereDate('date',$today))
            ->latest()->paginate(30);

        $currently_in = Attendance::with('member.user','seat')
            ->where('library_id',$library->id)
            ->whereDate('date',$today)->whereNull('check_out')->get();

        return view('owner.attendance.index', compact('attendance','currently_in','library'));
    }

    // QR Code for library entry
    public function qrCode()
    {
        $library = $this->library();
        $url = url('/checkin/' . $library->slug);
        $qr = QrCode::format('svg')->size(300)->generate($url);
        return view('owner.attendance.qr', compact('qr','library','url'));
    }

    // Student scans QR → this page
    public function checkInPage($slug)
    {
        $library = \App\Models\Library::where('slug',$slug)->where('status','active')->firstOrFail();
        return view('student.checkin', compact('library'));
    }

    // Process QR check-in via UID
    public function processCheckIn(Request $request)
    {
        $request->validate(['uid' => 'required', 'library_slug' => 'required']);
        $library = \App\Models\Library::where('slug',$request->library_slug)->firstOrFail();
        $member  = Member::where('uid',$request->uid)->where('library_id',$library->id)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found.']);
        }

        if ($member->status !== 'active' || $member->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Your plan has expired. Please renew.']);
        }

        $today = Carbon::today();
        $existing = Attendance::where('member_id',$member->id)->whereDate('date',$today)->whereNull('check_out')->first();

        if ($existing) {
            // Check out
            $existing->update(['check_out' => now()]);
            return response()->json(['success' => true, 'action' => 'checkout', 'message' => 'Checked out successfully!', 'member' => $member->user->name]);
        } else {
            // Check in
            Attendance::create([
                'library_id' => $library->id,
                'member_id'  => $member->id,
                'seat_id'    => $member->seat_id,
                'check_in'   => now(),
                'date'       => $today,
            ]);
            return response()->json(['success' => true, 'action' => 'checkin', 'message' => 'Checked in successfully!', 'member' => $member->user->name, 'seat' => $member->seat?->seat_number, 'days_left' => $member->daysLeft()]);
        }
    }
}
