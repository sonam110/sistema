<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\Producto;

class PurchaseOrderReceiving extends Model
{
    use SoftDeletes;
	
	protected $fillable = ['purchase_order_id', 'purchase_order_product_id', 'producto_id', 'receiving_token', 'received_qty'];

    public function purchaseOrder()
    {
    	return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function purchaseOrderProduct()
    {
    	return $this->belongsTo(PurchaseOrderProduct::class, 'purchase_order_product_id', 'id');
    }

    public function producto()
    {
    	return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
