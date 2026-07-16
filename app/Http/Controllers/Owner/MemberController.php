<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Models\Seat;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MemberController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index(Request $request)
    {
        $library = $this->library();
        $members = Member::with('user','seat','shift')
            ->where('library_id', $library->id)
            ->when($request->search, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('name','like',"%{$request->search}%")
                  ->orWhere('phone','like',"%{$request->search}%")))
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()->paginate(20);

        return view('owner.members.index', compact('members','library'));
    }

    /**
     * Builds the seat_id => occupancy-map array used by the create/edit forms
     * to grey out seats/shifts that are already booked, instead of a blanket
     * "any active member on this seat" disable that ignored shifts entirely.
     */
    private function seatAvailability($library, $shifts, ?int $excludeMemberId = null)
    {
        $seats = Seat::where('library_id', $library->id)->where('is_active', true)
            ->with(['members' => function ($q) {
                $q->where('status', 'active')
                  ->where(function ($q2) {
                      $q2->whereNull('plan_end_date')->orWhereDate('plan_end_date', '>=', now()->toDateString());
                  })
                  ->with('user', 'shift');
            }])
            ->orderBy('row_label')->orderBy('seat_number')->get();

        $availability = [];
        foreach ($seats as $seat) {
            $availability[$seat->id] = $seat->occupancyMap($shifts, $excludeMemberId, $seat->members);
        }

        return [$seats, $availability];
    }

    public function create(Request $request)
    {
        $library = $this->library();
        $shifts  = Shift::where('library_id',$library->id)->where('is_active',true)->orderBy('start_time')->get();
        [$seats, $seatAvailability] = $this->seatAvailability($library, $shifts);

        $prefillSeatId  = $request->query('seat_id');
        $prefillShiftId = $request->query('shift_id');

        return view('owner.members.create', compact('seats','shifts','library','seatAvailability','prefillSeatId','prefillShiftId'));
    }

    public function store(Request $request)
    {
        $library = $this->library();
        $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|digits:10',
            'seat_id'         => 'nullable|exists:seats,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'plan_start_date' => 'required|date',
            'plan_end_date'   => 'required|date|after:plan_start_date',
        ]);

        if ($request->seat_id) {
            $seat  = Seat::where('library_id', $library->id)->findOrFail($request->seat_id);
            $shift = $request->shift_id ? Shift::findOrFail($request->shift_id) : null;
            if ($seat->isOccupiedForShift($shift)) {
                return back()->withInput()->with('error', 'That seat is not available for the selected shift. Please pick a free seat or shift.');
            }
        }

        $user = User::create([
            'library_id' => $library->id,
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->phone), // Default: phone number
            'role'       => 'student',
            'is_active'  => true,
        ]);

        $member = Member::create([
            'library_id'      => $library->id,
            'user_id'         => $user->id,
            'seat_id'         => $request->seat_id,
            'shift_id'        => $request->shift_id,
            'dob'             => $request->dob,
            'address'         => $request->address,
            'aadhar'          => $request->aadhar,
            'status'          => 'active',
            'plan_start_date' => $request->plan_start_date,
            'plan_end_date'   => $request->plan_end_date,
        ]);

        return redirect('/owner/members')->with('success', 'Member added successfully. UID: ' . $member->uid);
    }

    public function show(Member $member)
    {
        $library = $this->library();
        if ($member->library_id !== $library->id) abort(403);
        $member->load('user','seat','shift','feePayments','attendance');
        return view('owner.members.show', compact('member','library'));
    }

    public function edit(Member $member)
    {
        $library = $this->library();
        if ($member->library_id !== $library->id) abort(403);
        $shifts = Shift::where('library_id',$library->id)->where('is_active',true)->orderBy('start_time')->get();
        [$seats, $seatAvailability] = $this->seatAvailability($library, $shifts, $member->id);
        $member->load('user');
        return view('owner.members.edit', compact('member','seats','shifts','library','seatAvailability'));
    }

    public function update(Request $request, Member $member)
    {
        $library = $this->library();
        if ($member->library_id !== $library->id) abort(403);

        $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|digits:10',
            'seat_id'         => 'nullable|exists:seats,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'plan_end_date'   => 'required|date',
        ]);

        if ($request->seat_id) {
            $seat  = Seat::where('library_id', $library->id)->findOrFail($request->seat_id);
            $shift = $request->shift_id ? Shift::findOrFail($request->shift_id) : null;
            if ($seat->isOccupiedForShift($shift, $member->id)) {
                return back()->withInput()->with('error', 'That seat is not available for the selected shift. Please pick a free seat or shift.');
            }
        }

        $member->user->update(['name' => $request->name, 'phone' => $request->phone]);
        $member->update([
            'seat_id'       => $request->seat_id,
            'shift_id'      => $request->shift_id,
            'dob'           => $request->dob,
            'address'       => $request->address,
            'status'        => $request->status,
            'plan_end_date' => $request->plan_end_date,
        ]);

        return redirect('/owner/members')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $library = $this->library();
        if ($member->library_id !== $library->id) abort(403);
        $member->delete();
        return back()->with('success', 'Member removed.');
    }
}
