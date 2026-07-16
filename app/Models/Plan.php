<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    protected $fillable = [
        'name','description','price','trial_days',
        'max_branches','staff_accounts','white_label','is_active'
    ];

    public function libraries() { return $this->hasMany(Library::class); }
}
