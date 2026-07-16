<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'library_id','name','email','phone','password','role','is_active'
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    public function library() { return $this->belongsTo(Library::class); }
    public function member() { return $this->hasOne(Member::class); }

    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isStaff(): bool { return $this->role === 'staff'; }
    public function isStudent(): bool { return $this->role === 'student'; }
}
