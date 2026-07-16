<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Library extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','slug','email','phone','address','city','state',
        'logo','stamp','banner','tagline','theme_color',
        'plan_id','status','trial_ends_at','plan_expires_at'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'plan_expires_at' => 'datetime',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function users() { return $this->hasMany(User::class); }
    public function members() { return $this->hasMany(Member::class); }
    public function seats() { return $this->hasMany(Seat::class); }
    public function shifts() { return $this->hasMany(Shift::class); }
    public function feePayments() { return $this->hasMany(FeePayment::class); }
    public function attendance() { return $this->hasMany(Attendance::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function announcements() { return $this->hasMany(Announcement::class); }

    public function isActive(): bool
    {
        return $this->status === 'active' &&
               ($this->plan_expires_at === null || $this->plan_expires_at->isFuture());
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function daysLeft(): int
    {
        if ($this->plan_expires_at) {
            return max(0, now()->diffInDays($this->plan_expires_at, false));
        }
        return 0;
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-library.png');
    }
}
