<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\booking;
use App\BookingPaymentThrough;
use App\User;

class BookingInstallmentPaid extends Model
{
    protected $fillable = [
        'booking_id', 'booking_payment_through_id', 'created_by', 'amount'
    ];

    public function booking()
    {
        return $this->belongsTo(booking::class, 'booking_id', 'id');
    }

    public function bookingPaymentThroughs()
    {
        return $this->hasMany(BookingPaymentThrough::class, 'booking_payment_through_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
