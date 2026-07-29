<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $fillable = ['library_id','name','start_time','end_time','price','is_active'];
    public function library() { return $this->belongsTo(Library::class); }
    public function members() { return $this->hasMany(Member::class); }

    /**
     * Whether this shift's daily time window overlaps another shift's window.
     * Handles overnight shifts (end_time <= start_time) by treating them as
     * wrapping past midnight.
     */
    public function overlaps(Shift $other): bool
    {
        if ($this->id === $other->id) {
            return true;
        }

        [$start1, $end1] = $this->minutesRange();
        [$start2, $end2] = $other->minutesRange();

        return $start1 < $end2 && $start2 < $end1;
    }

    private function minutesRange(): array
    {
        $start = Carbon::parse($this->start_time);
        $end   = Carbon::parse($this->end_time);
        $startMin = $start->hour * 60 + $start->minute;
        $endMin   = $end->hour * 60 + $end->minute;
        if ($endMin <= $startMin) {
            $endMin += 1440;
        }
        return [$startMin, $endMin];
    }

    /**
     * The actual moment this shift's window closes for a given calendar day
     * — e.g. for a Morning shift (6am-12pm) that's noon the same day, but for
     * an overnight or 24-hour shift (end_time <= start_time, like Night
     * 18:00-00:00 or a Full Day 00:00-00:00 shift) the window only closes
     * after crossing midnight, so it lands on the following day instead.
     * Without this, comparing end_time as a plain "H:i:s" string against the
     * current time flags every overnight/24-hour shift as "ended" the moment
     * it starts, since a small end_time like 00:00:00 looks smaller than any
     * later clock time on the same day.
     */
    public function endBoundaryFor(\Carbon\Carbon $day): \Carbon\Carbon
    {
        $start = Carbon::parse($day->format('Y-m-d') . ' ' . $this->start_time);
        $end   = Carbon::parse($day->format('Y-m-d') . ' ' . $this->end_time);

        if ($end->lte($start)) {
            $end->addDay();
        }

        return $end;
    }

    /**
     * Human-friendly time range — flags the start==end case as a 24-hour
     * shift instead of showing a confusing "12:00 AM - 12:00 AM".
     */
    public function getTimeLabelAttribute(): string
    {
        $start = Carbon::parse($this->start_time);
        $end   = Carbon::parse($this->end_time);

        if ($start->format('H:i') === $end->format('H:i')) {
            return '24 Hours (Full Day)';
        }

        return $start->format('h:i A') . ' - ' . $end->format('h:i A');
    }
}
