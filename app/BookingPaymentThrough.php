<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\booking;

class BookingPaymentThrough extends Model
{
    protected $fillable = [
        'booking_id', 'payment_mode', 'amount', 'no_of_installment', 'installment_amount', 'is_installment_complete', 'cheque_number', 'bank_detail'
    ];

    public function booking()
    {
        return $this->belongsTo(booking::class, 'booking_id', 'id');
    }
}
