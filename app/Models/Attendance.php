<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['library_id','member_id','seat_id','check_in','check_out','date'];
    protected $casts = ['check_in'=>'datetime','check_out'=>'datetime','date'=>'date'];

    public function library() { return $this->belongsTo(Library::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function seat() { return $this->belongsTo(Seat::class); }

    public function duration(): string
    {
        if (!$this->check_out) return 'In Library';
        $mins = $this->check_in->diffInMinutes($this->check_out);
        return floor($mins/60) . 'h ' . ($mins%60) . 'm';
    }
}
