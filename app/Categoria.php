<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

	protected $fillable = [
        'nombre', 'descripcion', 'mla_category_id', 'orden', 'activo',
    ];
	public $timestamps = false;

	public function categoriahasproductos(){
	  	return $this->hasMany(Producto::class, 'categoria_id');
	}


	public function getname(){
		return $this->name;
	}
  //
}
