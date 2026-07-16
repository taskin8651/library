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
}
