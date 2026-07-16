<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'library_id','user_id','seat_id','shift_id','uid','profile_photo',
        'dob','address','aadhar','status','plan_start_date','plan_end_date'
    ];
    protected $casts = ['plan_start_date'=>'date','plan_end_date'=>'date','dob'=>'date'];

    public function library() { return $this->belongsTo(Library::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function seat() { return $this->belongsTo(Seat::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
    public function feePayments() { return $this->hasMany(FeePayment::class); }
    public function attendance() { return $this->hasMany(Attendance::class); }

    public function isExpired(): bool
    {
        return $this->plan_end_date && $this->plan_end_date->isPast();
    }

    public function daysLeft(): int
    {
        return $this->plan_end_date ? max(0, now()->diffInDays($this->plan_end_date, false)) : 0;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($member) {
            $member->uid = strtoupper(substr(uniqid(), -6));
        });
    }
}
