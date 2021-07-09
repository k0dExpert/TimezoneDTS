<?php

return [
    'db' => [
        'dsn' => 'mysql:host=mysql;dbname=timezone',
        'user' => 'user',
        'pass' => 'password',
    ],
    'routes' => [
        'url_prefix' => '',
        'controllers' => [
            'time' => 'Mirai\\Timezone\\Controller\\TimeController',
        ]
    ],
    'time' => [
//        'adapter' => 'Mirai\\Timezone\\Time\\TimeAdapterTimezonedb',
        'adapter' => 'Mirai\\Timezone\\Time\\TimeAdapterCsv',
        'settings' => [
            'timezonedb' => [
                'url' => 'http://api.timezonedb.com/v2.1/get-time-zone',
                'api_key' => ''
            ],
            'csv' => [
                'path' => dirname(__FILE__).'/../public/timezone.csv'
            ]
        ]
    ]
];