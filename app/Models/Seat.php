<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['library_id','seat_number','row_label','type','is_active','status'];
    public function library() { return $this->belongsTo(Library::class); }
    public function members() { return $this->hasMany(Member::class); }
    public function attendance() { return $this->hasMany(Attendance::class); }

    public function isOccupied(): bool
    {
        return $this->members()->where('status','active')->exists();
    }
}
