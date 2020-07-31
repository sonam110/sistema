<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{

	protected $fillable = [
        'nombre', 'des_corta', 'descripcion', 'postura_id', 'tecnologia_id', 'garantia_id', 'item_id', 'orden', 'activo'
    ];
  	public $timestamps = false;

	public function modelohasproductos(){
		return $this->hasMany(Product::class, 'modelo_id'.'id');
	}

	public function getname(){
		return $this->nombre;
	}

	public function postura(){
		return $this->hasOne(Postura::class, 'id', 'postura_id');
	}

	public function tecnologia(){
		return $this->hasOne(Postura::class, 'id', 'tecnologia_id');
	}

	public function garantia(){
		return $this->hasOne(Postura::class, 'id', 'garantia_id');
	}

	public function item(){
		return $this->hasOne(Item::class, 'id', 'item_id');
	}
    //
}
