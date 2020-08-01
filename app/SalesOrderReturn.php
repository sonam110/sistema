<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\booking;
use App\bookeditem;
use App\Producto;

class SalesOrderReturn extends Model
{
    use SoftDeletes;
	
	protected $fillable = ['booking_id', 'bookeditem_id', 'producto_id', 'return_token', 'return_qty', 'return_note'];

    public function booking()
    {
    	return $this->belongsTo(booking::class, 'booking_id', 'id');
    }

    public function bookeditem()
    {
    	return $this->belongsTo(bookeditem::class, 'bookeditem_id', 'id');
    }

    public function producto()
    {
    	return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
