<?php
return $config = [
    'app_id' => env('ML_APP_ID',''),
    'secret_key' => env('ML_SECRET_KEY',''),
    'redirect_uri' => 'https://dormicentro.com/',
    'production' => env('ML_PRODUCTION',''),
    'country' => env('ML_COUNTRY_CODE','')
];