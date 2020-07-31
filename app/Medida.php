<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
  protected $fillable = [
        'nombre', 'alias'
    ];
  public $timestamps = false;
}
