<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index()
    {
        $library = $this->library();
        $announcements = Announcement::where('library_id', $library->id)->latest()->paginate(20);
        return view('owner.announcements.index', compact('announcements', 'library'));
    }

    public function store(Request $request)
    {
        $library = $this->library();
        $request->validate([
            'title'           => 'required|string|max:150',
            'message'         => 'required|string|max:2000',
            'type'            => 'required|in:info,warning,success,danger',
            'target_audience' => 'nullable|in:all,active,expiring',
            'scheduled_at'    => 'nullable|date',
        ]);

        Announcement::create([
            'library_id'      => $library->id,
            'title'           => $request->title,
            'message'         => $request->message,
            'type'            => $request->type,
            'is_active'       => true,
            'target_audience' => $request->target_audience ?? 'all',
            'scheduled_at'    => $request->scheduled_at,
        ]);

        $when = $request->scheduled_at && \Carbon\Carbon::parse($request->scheduled_at)->isFuture()
            ? 'Announcement scheduled for ' . \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y, h:i A') . '.'
            : 'Announcement posted.';

        return back()->with('success', $when);
    }

    public function toggle(Announcement $announcement)
    {
        $library = $this->library();
        if ($announcement->library_id !== $library->id) abort(403);
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', 'Announcement status updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $library = $this->library();
        if ($announcement->library_id !== $library->id) abort(403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
