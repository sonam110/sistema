<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;

class Producto extends Model
{
	use Sluggable;
	protected $fillable = [
        'nombre', 'item_id', 'categoria_id', 'marca_id', 'modelo_id', 'medida_id', 'altura_id', 'precio', 'stock', 'tecnologia_id', 'garantia_id', 'postura_id', 'oferta', 'destacado', 'novedad', 'activo', 'disponible','publicable','slug', 'item_imagen', 'imagen', 'mla_id', 'shipping_mode','sync_date'
    ];
	public $timestamps = false;

	public function getSlugAttribute(): string
	{
		return Str::slug($this->nombre);
	}


	public function getUrlAttribute(): string
	{
		return action('ProductosController@showproducto', [$this->id, $this->slug]);
	}

	public function imagens()
	{
		return $this->belongsToMany(Imagen::class, 'producto_imagen','producto_id', 'imagen_id')->withPivot('imagen_id')->orderBy('pivot_imagen_id', 'asc');
		// , 'producto_id', 'imagen_id'
	}
	public function categoria()
	{
		return $this->belongsTo(Categoria::class,'categoria_id' );
	}

	public function marca() {
		return $this->belongsTo(Marca::class, 'marca_id');
	}

	public function modelo() {
		return $this->belongsTo(Modelo::class, 'modelo_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function item() {
		return $this->belongsTo(Item::class, 'item_id');
	}

	public function altura() {
			return $this->hasOne(Altura::class, 'id','altura_id');
	}

	public function garantia() {
			return $this->hasOne(Garantia::class, 'id','garantia_id');
	}


	public function medida() {
			return $this->hasOne(Medida::class, 'id', 'medida_id');
	}


	public function postura() {
			return $this->hasOne(Postura::class, 'id','postura_id');
	}


	public function tecnologia() {
			return $this->hasOne(Tecnologia::class, 'id','tecnologia_id');
	}
	//**************************************************************************************
	public static function precios($id){
		return Producto::where('item_id','=',$id)->get();
	}
	//**************************************************************************************
	public function getnombre(){
		return $this->nombre;
	}
	public function mostrarItem($item)
	{
		return $this->item_id;
		}
	//***********************************************************************


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

  public function getRouteKeyName()
    {
        return 'slug';
    }
    //
}
