<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PurchaseOrder;
use App\Producto;
use App\PurchaseOrderReceiving;
use App\PurchaseOrderReturn;

class PurchaseOrderProduct extends Model
{
	use SoftDeletes;
	
	protected $fillable = ['purchase_order_id', 'producto_id', 'required_qty', 'price', 'accept_qty', 'return_qty', 'receiving_status', 'complete_date'];

    public function purchaseOrder()
    {
    	return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function producto()
    {
    	return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function purchaseOrderReceivings()
    {
        return $this->hasMany(PurchaseOrderReceiving::class, 'purchase_order_id', 'id');
    }

    public function purchaseOrderReturns()
    {
        return $this->hasMany(PurchaseOrderReturn::class, 'purchase_order_id', 'id');
    }
}
