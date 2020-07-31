<?php

namespace App;
												
use Illuminate\Database\Eloquent\Model;
use App\bookeditem;
use App\User;
																															
class booking extends Model
{
    protected $fillable = [
        'userId', 'email', 'country', 'firstname', 'lastname', 'companyname', 'address1', 'address2', 'city', 'state', 'postcode', 'phone', 'shipping_email', 'shipping_country', 'shipping_firstname', 'shipping_lastname', 'shipping_companyname', 'shipping_address1', 'shipping_address2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_phone', 'orderNote', 'tranjectionid', 'amount', 'installments', 'interestAmount', 'payableAmount','paymentThrough', 'orderstatus', 'deliveryStatus','due_condition','address_validation_code','ip_address'
    ];

    public function productDetails() {
        return $this->belongsToMany('App\Producto', 'bookeditems', 'bookingId', 'itemid')->select('productos.nombre', 'productos.imagen', 'bookeditems.itemqty', 'bookeditems.itemPrice');
    }

    public function getCardInfo() {
        return $this->hasOne('App\CardInfo', 'bookingId', 'id');
    }

    public function getBookeditem() {
        return $this->hasMany(bookeditem::class, 'bookingId', 'id');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
