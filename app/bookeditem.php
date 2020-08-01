<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;

class bookeditem extends Model
{
    protected $fillable = [
        'bookingId', 'itemid', 'itemqty' ,'return_qty', 'itemPrice'
    ];

    public function producto() {
        return $this->belongsTo(Producto::class, 'itemid', 'id');
    }
}
