<?php

return [
    'sipay' => [
        'api_url'=> env('SIPAY_API_URL', null),
        'api_url_array'=> [
            env('DEV_SIPAY_API_URL', null),
            env('PROV_SIPAY_API_URL', null),
            env('LIVE_SIPAY_API_URL', null),
        ],
        'api_merchant_key'=> env('SIPAY_MERCHANT_KEY', null),
        'app_key'=> env('SIPAY_APP_KEY', null),
        'app_secret'=> env('SIPAY_APP_SECRET', null),
        'merchant_id'=> env('SIPAY_MERCHANT_ID', null),

        'dev' => [
            'api_url'=> env('SIPAY_DEV_API_URL', null),
            'api_merchant_key'=> env('SIPAY_DEV_MERCHANT_KEY', null),
            'app_key'=> env('SIPAY_DEV_APP_KEY', null),
            'app_secret'=> env('SIPAY_DEV_APP_SECRET', null),
            'merchant_id'=> env('SIPAY_DEV_MERCHANT_ID', null),
        ],
        'prov' => [
            'api_url'=> env('SIPAY_PROV_API_URL', null),
            'api_merchant_key'=> env('SIPAY_PROV_MERCHANT_KEY', null),
            'app_key'=> env('SIPAY_PROV_APP_KEY', null),
            'app_secret'=> env('SIPAY_PROV_APP_SECRET', null),
            'merchant_id'=> env('SIPAY_PROV_MERCHANT_ID', null),
        ]
    ]
];
