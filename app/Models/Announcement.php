<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['library_id','title','message','type','is_active','target_audience','scheduled_at'];
    protected $casts = ['is_active' => 'boolean', 'scheduled_at' => 'datetime'];

    public function library() { return $this->belongsTo(Library::class); }

    public function isScheduledForFuture(): bool
    {
        return $this->scheduled_at !== null && $this->scheduled_at->isFuture();
    }
}
