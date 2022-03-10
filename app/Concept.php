<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PurchaseOrder;

class Concept extends Model
{
    protected $table = 'purchase_concepts';
    protected $fillable = [
        'description'
    ];

    public function purchaseOrders()
    {
    	return $this->hasMany(PurchaseOrder::class, 'concept_id', 'id');
    }
}
