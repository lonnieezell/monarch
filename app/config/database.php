<?php

return [
    'default' => [
        'driver' => env('DB_DRIVER_DEFAULT', 'mysql'),
        'host' => env('DB_HOST_DEFAULT', 'localhost'),
        'port' => env('DB_PORT_DEFAULT', 3306),
        'user' => env('DB_USER_DEFAULT', 'root'),
        'password' => env('DB_PASSWORD_DEFAULT', 'root'),
        'database' => env('DB_DATABASE_DEFAULT', 'app'),
    ],

    'test' => [
        'driver' => env('DB_DRIVER_DEFAULT', 'sqlite'),
        'database' => env('DB_DATABASE_DEFAULT', ':memory:'),
    ],
];
