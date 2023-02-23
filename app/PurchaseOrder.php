<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Supplier;
use App\Concept;
use App\PurchaseOrderProduct;
use App\PurchaseOrderReceiving;
use App\PurchaseOrderReturn;

class PurchaseOrder extends Model
{
	use SoftDeletes;
	
    const PO_NUMBER_PREFIX  = 'DORMI'; //change po prefix number
    const PO_NUMBER_DIGIT   = 6; //change po number digit 6 = XXXXXX

    protected $fillable = ['supplier_id', 'po_date', 'po_no', 'total_amount', 'tax_percentage', 'tax_amount', 'gross_amount', 'po_status', 'po_completed_date', 'remark', 'is_read_token', 'is_read_status'];

    public function supplier()
    {
    	return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function Concept()
    {
        return $this->belongsTo(Concept::class, 'concept_id', 'id');
    }

    public function purchaseOrderProducts()
    {
    	return $this->hasMany(PurchaseOrderProduct::class, 'purchase_order_id', 'id');
    }

    public function purchaseOrderReceivings()
    {
        return $this->hasMany(PurchaseOrderReceiving::class, 'purchase_order_id', 'id');
    }

    public function purchaseOrderReturns()
    {
        return $this->hasMany(PurchaseOrderReturn::class, 'purchase_order_id', 'id');
    }

    public function totalReturnAmount()
    {
        $totalReturnAmount = 0;
        foreach ($this->purchaseOrderReturns as $key => $product) {
            $totalReturnAmount += $product->return_price;
        }
        return number_format($totalReturnAmount, 2, '.', '');
    }
}
