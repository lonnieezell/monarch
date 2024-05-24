<?php

use Monarch\Database\Connection;

test('create with config', function () {
    $config = [
        'driver' => 'sqlite',
        'database' => ':memory:',
    ];

    $connection = Connection::createWithConfig($config);

    expect($connection)->toBeInstanceOf(Connection::class);
});

test('connect', function () {
    $config = [
        'driver' => 'sqlite',
        'database' => ':memory:',
    ];

    $connection = Connection::createWithConfig($config);
    $pdo = $connection->connect();

    expect($pdo)->toBeInstanceOf(PDO::class);
    expect($connection->pdo)->toBe($pdo);

    $connection->close();

    expect($connection->pdo)->toBeNull();
});

// Add more test methods as needed...