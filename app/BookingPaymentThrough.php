<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPaymentThrough extends Model
{
    protected $fillable = [
        'booking_id', 'payment_mode', 'amount', 'no_of_installment', 'installment_amount', 'cheque_number', 'bank_detail'
    ];
}
