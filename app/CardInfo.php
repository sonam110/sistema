<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    protected $fillable = ['bookingId','card_type','card_number','card_expiration_month','card_expiration_year','security_code','card_holder_name','card_holder_doc_type','card_holder_doc_number','card_holder_door_number','card_holder_birthday'];
}
