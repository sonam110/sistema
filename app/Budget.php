<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BudgetItem;
use App\User;
class Budget extends Model
{
    protected $fillable = [
        'customer_id','observation', 'total','tax_percentage','shipping_charge', 'tax_amount', 'payable_amount', 'created_by','status','ip_address','comment'
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function customer() {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

     public function getBudgetitem() {
        return $this->hasMany(BudgetItem::class, 'budget_id', 'id');
    }
    public function productDetails() {
        return $this->belongsToMany('App\Producto', 'budget_items', 'budget_id', 'itemid')->select('productos.nombre', 'productos.imagen', 'budget_items.itemqty', 'budget_items.itemPrice');
    }
}
