<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;

class bookeditem extends Model
{
    protected $fillable = [
        'bookingId', 'itemid', 'itemqty' ,'return_qty', 'itemPrice','shipping_company','shipping_charge','postcode','is_stock_updated_in_ml'
    ];

    public function producto() {
        return $this->belongsTo(Producto::class, 'itemid', 'id');
    }
}
