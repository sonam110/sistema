<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tecnologia extends Model
{
    protected $fillable = [
        'nombre', 'tecnologia_padre'
    ];
    public $timestamps = false;
}
