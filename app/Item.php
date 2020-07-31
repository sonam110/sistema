<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;

class Item extends Model
{
	use Sluggable;

	protected $fillable = [
        'nombre', 'marca_id', 'descuento', 'tecnologia_id', 'imagen_id', 'postura_id', 'categoria_id', 'destacado', 'novedad', 'activo', 'slug'
    ];
	public $timestamps = false;

	public function getSlugAttribute(): string
	{
			return Str::slug($this->nombre);
	}

	public function getUrlAttribute(): string
	{
		return action('ItemsController@showitem', [$this->id, $this->slug]);
	}


	public function categorias()
	{
		return $this->belongsToMany(Categoria::class, 'productos', 'item_id','categoria_id');
	}


	public function itemImagen()
	{
			return $this->hasOne(Imagen::class,  'id', 'imagen_id');
	}

	public function marca() {
		return $this->belongsTo(Marca::class, 'marca_id');
	}

	public function postura() {
			return $this->hasOne(Postura::class, 'id','postura_id');
	}


	public function tecnologia() {
			return $this->hasOne(Tecnologia::class, 'id','tecnologia_id');
	}

	//scopes
	public function scopeVisibles($query)
	{
		$query->where('visible', 1);
	}

	public function sluggable()
	{
	    return [
	        'slug' => [
	            'source' => 'title'
	        ]
	    ];
	}

	public function getname(){
		return $this->name;
	}
	public function getRouteKeyName()
	{
	    return 'slug';
	}
	//
}
	//
