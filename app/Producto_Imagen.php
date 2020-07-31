<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto_Imagen extends Model
{
    protected $table = 'producto_imagen';
    protected $fillable = [
        'producto_id', 'imagen_id'
    ];
    public $timestamps = false;
}
