<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['library_id','name','start_time','end_time','price','is_active'];
    public function library() { return $this->belongsTo(Library::class); }
    public function members() { return $this->hasMany(Member::class); }
}
