<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;
use App\Categoria;
use App\Marca;
use App\CouponDiscount;
class CouponCode extends Model
{
    protected $fillable = [
        'type', 'type_id', 'coupon_code','coupon_desc', 'coupon_expity', 'user_type'
    ];
    protected $appends = ['type_detail'];

    public function couponDiscount()
    {
         return $this->hasMany(CouponDiscount::class, 'coupon_id', 'id');
    }

    public function getTypeDetailAttribute()
    {
        $name = '';
        if(@$this->attributes['type']=='Item'){
            $item = Item::select('id','nombre')->where('id',$this->attributes['type_id'])->first();
            $name =  @$item->nombre;
        }
        if(@$this->attributes['type']=='Marca'){
            $Marca = Marca::select('id','nombre')->where('id',$this->attributes['type_id'])->first();
            $name =   @$Marca->nombre;
            
        } 
        if(@$this->attributes['type']=='categoría'){
            $Categoria = Categoria::select('id','nombre')->where('id',$this->attributes['type_id'])->first();
            $name =   @$Categoria->nombre;
           
        }
        return $name ;

    }
}
