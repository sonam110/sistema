<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookeditemGeneric extends Model
{
    protected $fillable = [
        'booking_id', 'item_name', 'itemqty' ,'return_qty', 'itemPrice'
    ];
}
