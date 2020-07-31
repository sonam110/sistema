<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterestRate extends Model
{
    protected $fillable = [
        'int_type', 'month_1', 'month_2','month_3','month_4','month_5','month_6','month_7','month_8','month_9','month_10','month_11','month_12','month_13','month_14','month_15','month_16','month_17','month_18','month_19','month_20','month_21','month_22','month_23','month_24'
    ];
}
