<?php

namespace App;
												
use Illuminate\Database\Eloquent\Model;
use App\bookeditem;
use App\User;
use App\SalesOrderReturn;
use App\BookingPaymentThrough;
																															
class booking extends Model
{
    protected $fillable = [
        'userId', 'email', 'country', 'firstname', 'lastname', 'companyname', 'address1', 'address2', 'city', 'state', 'postcode', 'phone', 'shipping_email', 'shipping_country', 'shipping_firstname', 'shipping_lastname', 'shipping_companyname', 'shipping_address1', 'shipping_address2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_phone', 'orderNote', 'tranjectionid', 'amount', 'installments', 'interestAmount', 'tax_percentage', 'tax_percentage', 'payableAmount','paymentThrough', 'orderstatus', 'deliveryStatus','due_condition','address_validation_code','ip_address'
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

    public function salesOrderReturns()
    {
        return $this->hasMany(SalesOrderReturn::class, 'booking_id', 'id');
    }

    public function bookingPaymentThroughs()
    {
        return $this->hasMany(BookingPaymentThrough::class, 'booking_id', 'id');
    }

    public function totalReturnAmount()
    {
        $totalReturnAmount = 0;
        foreach ($this->salesOrderReturns as $key => $product) {
            $totalReturnAmount += $product->return_amount;
        }
        return number_format($totalReturnAmount, 2, '.', '');
    }
}
