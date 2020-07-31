<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bookeditem extends Model
{
    protected $fillable = [
        'bookingId', 'itemid', 'itemqty', 'itemPrice'
    ];
}
