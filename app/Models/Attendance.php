<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['library_id','member_id','seat_id','check_in','check_out','date','overstay_notified_at'];
    protected $casts = ['check_in'=>'datetime','check_out'=>'datetime','date'=>'date','overstay_notified_at'=>'datetime'];

    public function library() { return $this->belongsTo(Library::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function seat() { return $this->belongsTo(Seat::class); }

    public function duration(): string
    {
        if (!$this->check_out) return 'In Library';
        $mins = $this->check_in->diffInMinutes($this->check_out);
        return floor($mins/60) . 'h ' . ($mins%60) . 'm';
    }

    /**
     * Members still checked in today whose shift's end time has already
     * passed. Shared by the topbar bell, the owner dashboard banner, and
     * the scheduled email check, so the "what counts as overstayed" rule
     * only lives in one place.
     */
    public static function overstayedToday(int $libraryId)
    {
        $nowTimeStr = now()->format('H:i:s');

        return static::with(['member.user', 'member.shift', 'seat'])
            ->where('library_id', $libraryId)
            ->whereDate('date', today())
            ->whereNull('check_out')
            ->get()
            ->filter(function ($attendance) use ($nowTimeStr) {
                $shift = $attendance->member->shift ?? null;
                return $shift && $nowTimeStr > $shift->end_time;
            })
            ->values();
    }
}
