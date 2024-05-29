<?php

return [
    // The default database connection group to use
    'default' => env('DB_DRIVER', 'sqlite'),

    'sqlite' => [
        'driver' => env('DB_DRIVER', 'sqlite'),
        'database' => env('DB_DATABASE', WRITEPATH .'db.sq3'),
    ],

    'mysql' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', 'root'),
        'database' => env('DB_DATABASE', 'app'),
        'charset' => 'utf8mb4',
    ],

    'postgres' => [
        'driver' => env('DB_DRIVER', 'postgres'),
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', 'root'),
        'database' => env('DB_DATABASE', 'app'),
        'charset' => 'utf8mb4',
        'sslmode' => 'prefer',
    ],

    'test' => [
        'driver' => env('DB_DRIVER', 'sqlite'),
        'database' => env('DB_DATABASE', ':memory:'),
    ],
];
