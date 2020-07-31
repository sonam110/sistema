<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PurchaseOrder;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'email', 'company_name', 'address', 'city', 'state', 'phone', 'vat_number', 'status'
    ];

    public function purchaseOrders()
    {
    	return $this->hasMany(PurchaseOrder::class, 'supplier_id', 'id');
    }
}
