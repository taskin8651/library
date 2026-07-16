<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index()
    {
        $library = $this->library();
        $seats = Seat::with('members.user')
            ->where('library_id',$library->id)
            ->orderBy('row_label')->orderBy('seat_number')->get();

        $rows = $seats->groupBy('row_label');
        return view('owner.seats.index', compact('seats','rows','library'));
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
