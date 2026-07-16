<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['library_id','seat_number','row_label','type','is_active','status'];
    public function library() { return $this->belongsTo(Library::class); }
    public function members() { return $this->hasMany(Member::class); }
    public function attendance() { return $this->hasMany(Attendance::class); }

    public function activeMembers()
    {
        return $this->members()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('plan_end_date')->orWhereDate('plan_end_date', '>=', now()->toDateString());
            });
    }

    public function isOccupied(): bool
    {
        return $this->activeMembers()->exists();
    }

    public function isOccupiedForShift(?Shift $shift, ?int $excludeMemberId = null): bool
    {
        if (!$this->is_active || $this->status !== 'available') {
            return true;
        }

        $members = $this->activeMembers()->with('shift')
            ->when($excludeMemberId, fn($q) => $q->where('id', '!=', $excludeMemberId))
            ->get();

        if ($members->contains(fn($m) => is_null($m->shift_id))) {
            return true; // a full-day booking blocks every shift
        }

        if (!$shift) {
            return $members->isNotEmpty(); // requesting full-day is blocked by any existing booking
        }

        return $members->contains(fn($m) => $m->shift && $m->shift->overlaps($shift));
    }

    /**
     * Shift-by-shift occupancy breakdown for this seat, used by the seat map UI
     * and by the member seat/shift pickers. Pass an already-loaded members
     * collection (active, non-expired, with user+shift) to avoid N+1 queries.
     */
    public function occupancyMap($shifts, ?int $excludeMemberId = null, $preloadedMembers = null): array
    {
        $blocked = !$this->is_active || $this->status !== 'available';

        $result = [
            'blocked'        => $blocked,
            'blocked_reason' => $blocked ? (!$this->is_active ? 'inactive' : $this->status) : null,
            'full_day_taken' => null,
            'shifts'         => [],
        ];

        $members = $preloadedMembers ?? $this->activeMembers()->with(['user', 'shift'])->get();
        if ($excludeMemberId) {
            $members = $members->where('id', '!=', $excludeMemberId);
        }

        $fullDay = $members->firstWhere('shift_id', null);
        if ($fullDay) {
            $result['full_day_taken'] = [
                'member_id' => $fullDay->id,
                'name'      => $fullDay->user->name,
                'until'     => optional($fullDay->plan_end_date)->format('d M Y'),
            ];
        }

        foreach ($shifts as $shift) {
            $occupant = $fullDay ?: $members->first(fn($m) => $m->shift && $m->shift->overlaps($shift));
            $result['shifts'][$shift->id] = $occupant ? [
                'member_id' => $occupant->id,
                'name'      => $occupant->user->name,
                'until'     => optional($occupant->plan_end_date)->format('d M Y'),
                'full_day'  => is_null($occupant->shift_id),
            ] : null;
        }

        return $result;
    }
}
