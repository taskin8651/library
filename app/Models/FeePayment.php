<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'library_id','member_id','amount','payment_mode','upi_ref',
        'receipt_number','payment_date','valid_from','valid_till','collected_by','notes'
    ];
    protected $casts = ['payment_date'=>'date','valid_from'=>'date','valid_till'=>'date'];

    public function library() { return $this->belongsTo(Library::class); }
    public function member() { return $this->belongsTo(Member::class); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            $lastId = static::max('id') ?? 0;
            $payment->receipt_number = 'RCP-' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
