<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postura extends Model
{
    protected $fillable = [
        'nombre'
    ];
	public $timestamps = false;
}
