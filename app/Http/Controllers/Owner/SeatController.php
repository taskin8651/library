<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SeatController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index()
    {
        $library = $this->library();

        $shifts = Shift::where('library_id', $library->id)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $seats = Seat::where('library_id', $library->id)
            ->with(['members' => function ($q) {
                $q->where('status', 'active')
                  ->where(function ($q2) {
                      $q2->whereNull('plan_end_date')->orWhereDate('plan_end_date', '>=', now()->toDateString());
                  })
                  ->with('user', 'shift');
            }])
            ->orderBy('row_label')->orderBy('seat_number')->get();

        $rows = $seats->groupBy('row_label');

        $seatData = [];
        $stats = ['total' => $seats->count(), 'free' => 0, 'partial' => 0, 'full' => 0, 'blocked' => 0];

        foreach ($seats as $seat) {
            $map = $seat->occupancyMap($shifts, null, $seat->members);
            $totalShifts = $shifts->count();
            $bookedCount = ($map['blocked'] || $map['full_day_taken'])
                ? $totalShifts
                : collect($map['shifts'])->filter()->count();

            $seatData[$seat->id] = array_merge($map, [
                'id'           => $seat->id,
                'seat_number'  => $seat->seat_number,
                'row_label'    => $seat->row_label,
                'type'         => $seat->type,
                'is_active'    => $seat->is_active,
                'status'       => $seat->status,
                'booked_count' => $bookedCount,
                'total_shifts' => $totalShifts,
            ]);

            if ($map['blocked']) {
                $stats['blocked']++;
            } elseif ($totalShifts === 0) {
                $map['full_day_taken'] ? $stats['full']++ : $stats['free']++;
            } elseif ($bookedCount === 0) {
                $stats['free']++;
            } elseif ($bookedCount >= $totalShifts) {
                $stats['full']++;
            } else {
                $stats['partial']++;
            }
        }

        $shiftList = $shifts->map(fn($s) => [
            'id'    => $s->id,
            'name'  => $s->name,
            'time'  => Carbon::parse($s->start_time)->format('h:i A') . ' - ' . Carbon::parse($s->end_time)->format('h:i A'),
            'price' => $s->price,
        ]);

        return view('owner.seats.index', compact('seats', 'rows', 'library', 'shifts', 'seatData', 'stats', 'shiftList'));
    }

    public function store(Request $request)
    {
        $library = $this->library();
        $request->validate([
            'row_label'   => 'required|string|max:5',
            'seat_count'  => 'required|integer|min:1|max:50',
            'type'        => 'required|in:regular,cabin,vip',
        ]);

        $existing = Seat::where('library_id',$library->id)->where('row_label',$request->row_label)->max('seat_number');
        $startNum = $existing ? ((int) filter_var($existing, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;

        for ($i = $startNum; $i < $startNum + $request->seat_count; $i++) {
            Seat::create([
                'library_id'  => $library->id,
                'seat_number' => $request->row_label . $i,
                'row_label'   => strtoupper($request->row_label),
                'type'        => $request->type,
                'is_active'   => true,
            ]);
        }

        return back()->with('success', $request->seat_count . ' seats added in Row ' . $request->row_label);
    }

    public function destroy(Seat $seat)
    {
        $library = $this->library();
        if ($seat->library_id !== $library->id) abort(403);
        if ($seat->isOccupied()) return back()->with('error', 'Cannot delete an occupied seat.');
        $seat->delete();
        return back()->with('success', 'Seat ' . $seat->seat_number . ' deleted.');
    }

    public function toggle(Seat $seat)
    {
        $library = $this->library();
        if ($seat->library_id !== $library->id) abort(403);
        $seat->update(['is_active' => !$seat->is_active]);
        return back()->with('success', 'Seat status updated.');
    }

    public function setStatus(Request $request, Seat $seat)
    {
        $library = $this->library();
        if ($seat->library_id !== $library->id) abort(403);
        $request->validate(['status' => 'required|in:available,reserved,maintenance']);
        $seat->update(['status' => $request->status]);
        return back()->with('success', 'Seat ' . $seat->seat_number . ' marked as ' . ucfirst($request->status) . '.');
    }
}
