<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Supplier;
use App\Concept;
class SupplierInvoice extends Model
{
     public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function Concept()
    {
        return $this->belongsTo(Concept::class, 'concept_id', 'id');
    }
}
