<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

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

        return response()->json($this->attendanceService->toggle($member));
    }
}
