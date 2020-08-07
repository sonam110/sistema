<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\booking;
use App\User;

class BookingInstallmentPaid extends Model
{
    protected $fillable = [
        'booking_id', 'created_by', 'amount'
    ];

    public function booking()
    {
        return $this->belongsTo(booking::class, 'booking_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
