<?php


return [
    'mode'=> env('PAYMONGO_MODE','test'),

    'public' => [
        'test'=> env('PAYMONGO_PUBLIC_KEY_TEST'),
        'live'=> env('PAYMONGO_PUBLIC_KEY_LIVE')
    ],

    'secret'  => [
        'test'=> env('PAYMONGO_SECRET_KEY_TEST'),
        'live'=> env('PAYMONGO_SECRET_KEY_LIVE')
    ]
];