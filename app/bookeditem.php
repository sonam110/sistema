<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;
use App\booking;

class bookeditem extends Model
{
    protected $fillable = [
        'bookingId', 'itemid', 'itemqty' ,'return_qty', 'itemPrice','shipping_company','shipping_charge','postcode','is_stock_updated_in_ml'
    ];

    public function booking() {
        return $this->belongsTo(booking::class, 'bookingId', 'id');
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'itemid', 'id');
    }
}
