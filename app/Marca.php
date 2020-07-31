<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;

class Marca extends Model
{
  use Sluggable;

  protected $fillable = [
        'nombre', 'logo', 'orden', 'activo', 'slug'
    ];
  public $timestamps = false;

  public function getSlugAttribute(): string
	{
			return Str::slug($this->nombre);
	}
	public function getUrlAttribute(): string
  {
		return action('ProductosController@showitemspormarca', [$this->id, $this->slug]);
  }

  public function marcahasproductos()
  {
     return $this->hasMany(Product::class, 'marca_id');
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

  public function getname()
  {
    return $this->name;
  }
  public function getRouteKeyName()
  {
      return 'slug';
  }
  //
}
  //
