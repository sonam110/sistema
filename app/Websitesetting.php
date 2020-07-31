<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Websitesetting extends Model
{
    protected $tabel='websitesettings';
    protected $fillable = [
        'id', 'website_name', 'website_logo', 'website_title', 'email', 'locktimeout', 'address', 'mobilenum', 'facebook', 'twitter', 'googlePlus', 'applyCommType', 'applyComm', 'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6', 'day_7'
    ];
}
