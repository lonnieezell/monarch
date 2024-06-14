<?php

use Monarch\Database\Extensions\QueryBuilder;

return [
    // The default database connection group to use
    'default' => env('DB_DRIVER', 'sqlite'),

    'sqlite' => [
        'driver' => 'sqlite',
        'database' => env('DB_DATABASE', WRITEPATH .'db.sq3'),
    ],

    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', 'root'),
        'database' => env('DB_DATABASE', 'app'),
        'charset' => 'utf8mb4',
    ],

    'test' => [
        // 'driver' => env('DB_DRIVER', 'sqlite'),
        // 'database' => env('DB_DATABASE', ':memory:'),
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', 'root'),
        'database' => env('DB_DATABASE', 'monarch-test'),
        'charset' => 'utf8mb4',
    ],

    /**
     * Extensions provide additional functionality to the database,
     * such as QueryBuilder support.
     *
     * You can provide an array of classes to provide additional functionality
     * to the database connection object. When a method is not found on the
     * connection class itself, we will check the extensions for the method.
     * Once the method has been processed, the connection object will be returned
     * so that methods may continue to be chained.
     */
    'extensions' => [
        QueryBuilder::class,
    ],

    /**
     * QueryBuilder Extensions provide additional functionality to the QueryBuilder
     * object. You can provide an array of classes to provide additional functionality
     * to the QueryBuilder object. When a method is not found on the QueryBuilder class
     * itself, we will check the extensions for the method.
     */
    'queryBuilderExtensions' => [
        //
    ],
];
