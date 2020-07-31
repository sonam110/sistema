<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Imagen extends Model
{
  protected $fillable = [
        'nombre', 'item_imagen'
    ];
  public $timestamps = false;

  public function delModelo() {
   return $this->belongsTo(Modelo::class, 'modelo_id');
 }
  public function productos() {
   return $this->belongsToMany(Producto::class, 'producto_imagen');
 }
 public function delItem() {
  return $this->belongsTo(Item::class, 'imagen_id');
}

// , 'imagen_id', 'producto_id'
}
