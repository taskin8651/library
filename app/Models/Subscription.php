<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'library_id','plan_id','payment_method','razorpay_order_id','razorpay_payment_id',
        'razorpay_subscription_id','utr','amount','status','starts_at','expires_at'
    ];
    protected $casts = ['starts_at'=>'datetime','expires_at'=>'datetime'];

    public function library() { return $this->belongsTo(Library::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
}
