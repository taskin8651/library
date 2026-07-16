<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index()
    {
        $library = $this->library();
        $shifts = Shift::where('library_id', $library->id)
            ->withCount(['members' => function ($q) {
                $q->where('status', 'active')
                  ->where(function ($q2) {
                      $q2->whereNull('plan_end_date')->orWhereDate('plan_end_date', '>=', now()->toDateString());
                  });
            }])
            ->orderBy('start_time')->get();

        return view('owner.shifts.index', compact('shifts', 'library'));
    }

    public function store(Request $request)
    {
        $library = $this->library();
        $request->validate([
            'name'       => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'price'      => 'required|numeric|min:0',
        ]);

        Shift::create([
            'library_id' => $library->id,
            'name'       => $request->name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'price'      => $request->price,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Shift "' . $request->name . '" created. It can now be sold from the Seat Layout page.');
    }

    public function update(Request $request, Shift $shift)
    {
        $library = $this->library();
        if ($shift->library_id !== $library->id) abort(403);

        $request->validate([
            'name'       => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'price'      => 'required|numeric|min:0',
        ]);

        $shift->update($request->only('name', 'start_time', 'end_time', 'price'));

        return back()->with('success', 'Shift updated successfully.');
    }

    public function toggle(Shift $shift)
    {
        $library = $this->library();
        if ($shift->library_id !== $library->id) abort(403);
        $shift->update(['is_active' => !$shift->is_active]);
        return back()->with('success', 'Shift status updated.');
    }

    public function destroy(Shift $shift)
    {
        $library = $this->library();
        if ($shift->library_id !== $library->id) abort(403);

        $hasActiveMembers = $shift->members()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('plan_end_date')->orWhereDate('plan_end_date', '>=', now()->toDateString());
            })->exists();

        if ($hasActiveMembers) {
            return back()->with('error', 'Cannot delete a shift that has active members booked in it.');
        }

        $shift->delete();
        return back()->with('success', 'Shift "' . $shift->name . '" deleted.');
    }
}
