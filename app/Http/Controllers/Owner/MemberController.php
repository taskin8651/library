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

    public function create()
    {
        $library = $this->library();
        $seats   = Seat::where('library_id',$library->id)->where('is_active',true)->get();
        $shifts  = Shift::where('library_id',$library->id)->where('is_active',true)->get();
        return view('owner.members.create', compact('seats','shifts','library'));
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
        $seats  = Seat::where('library_id',$library->id)->where('is_active',true)->get();
        $shifts = Shift::where('library_id',$library->id)->where('is_active',true)->get();
        $member->load('user');
        return view('owner.members.edit', compact('member','seats','shifts','library'));
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
