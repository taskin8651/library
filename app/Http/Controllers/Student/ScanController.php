<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    public function page()
    {
        $user   = Auth::user();
        $member = $user->member;
        $library = $user->library;

        if (!$member) return view('student.no_member', compact('user', 'library'));

        return view('student.scan', compact('library'));
    }

    public function checkin(Request $request)
    {
        $request->validate(['slug' => 'required|string']);

        $user   = Auth::user();
        $member = $user->member;
        $library = $user->library;

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'No membership found on your account.']);
        }

        // The scanned QR must belong to this student's own library.
        if ($request->slug !== $library->slug) {
            return response()->json(['success' => false, 'message' => 'This QR code belongs to a different library.']);
        }

        return response()->json($this->attendanceService->toggle($member));
    }
}
