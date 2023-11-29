<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
     protected $fillable = [
        'budget_id', 'itemid', 'itemqty', 'itemPrice'
    ];
}
