<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Supplier;
class SupplierInvoice extends Model
{
     public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
