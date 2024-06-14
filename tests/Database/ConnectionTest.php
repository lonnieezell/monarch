<?php

use Monarch\Database\Connection;
use Monarch\Database\DriverInterface;
use Monarch\Database\Drivers\MySQLDriver;
use Monarch\Database\Drivers\SQLiteDriver;
use Monarch\Database\Extensions\QueryBuilder;

describe('Database Connection', function () {
    test('create with config', function () {
        $config = config('database.'. config('database.default'));

        $connection = Connection::createWithConfig($config);
        expect($connection)->toBeInstanceOf(DriverInterface::class);
        expect(get_class($connection))->toBeIn([
            SQLiteDriver::class,
            MySQLDriver::class,
        ]);
    });

    test('create with config should return the same instance', function () {
        $config = config('database.'. config('database.default'));

        $connection1 = Connection::createWithConfig($config);
        $connection2 = Connection::createWithConfig($config);

        expect($connection1)->toBe($connection2);
    });

    test('create with config should return different instances', function () {
        $config1 = config('database.'. config('database.default'));
        $config2 = config('database.test');

        $connection1 = Connection::createWithConfig($config1);
        $connection2 = Connection::createWithConfig($config2);

        expect($connection1)->not->toBe($connection2);
    });

    test('create with config should throw exception if driver is missing', function () {
        $config = [
            'database' => ':memory:',
        ];

        expect(fn () => Connection::createWithConfig($config))->toThrow(new RuntimeException('Database driver is required'));
    });

    test('create with config should throw exception if driver is unsupported', function () {
        $config = [
            'driver' => 'foo',
            'database' => ':memory:',
        ];

        expect(fn () => Connection::createWithConfig($config))->toThrow(new RuntimeException('Unsupported database driver: foo'));
    });

    test('loadExtensions', function () {
        $config = config('database.'. config('database.default'));
        $connection = Connection::createWithConfig($config);

        $extensions = [
            'Monarch\Database\Extensions\QueryBuilder',
        ];

        $connection->loadExtensions($extensions);
        expect($connection->extensions())->toHaveKey('sql');
    });

    test('loadExtensions should throw exception if extension is not found', function () {
        $config = config('database.'. config('database.default'));
        $connection = Connection::createWithConfig($config);

        $extensions = [
            'Monarch\Database\Extensions\Foo',
        ];

        expect(fn () => $connection->loadExtensions($extensions))->toThrow(new RuntimeException('Extension not found: Monarch\Database\Extensions\Foo'));
    });

    test('loaded extensions can be accessed', function () {
        $config = config('database.'. config('database.default'));
        $connection = Connection::createWithConfig($config);

        $extensions = [
            'Monarch\Database\Extensions\QueryBuilder',
        ];

        $connection->loadExtensions($extensions);
        $query = $connection->sql('SELECT * FROM users');
        expect($query)->toBeInstanceOf(QueryBuilder::class);
    });

    test('run query', function () {
        $config = config('database.'. config('database.default'));
        $connection = Connection::createWithConfig($config);

        $connection->run('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $connection->run('INSERT INTO users (name) VALUES (?)', ['foo']);
        $connection->run('INSERT INTO users (name) VALUES (?)', ['bar']);

        $users = $connection->run('SELECT * FROM users')->fetchAll();
        expect($users)->toHaveCount(2);

        $connection->dropTable('users');
    });

    test('run query with QueryBuilder', function () {
        $config = config('database.'. config('database.default'));
        $connection = Connection::createWithConfig($config);

        $connection->run('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $connection->run('INSERT INTO users (name) VALUES (?)', ['foo']);
        $connection->run('INSERT INTO users (name) VALUES (?)', ['bar']);

        $users = $connection->sql('SELECT * FROM users')->run()->fetchAll();
        expect($users)->toHaveCount(2);

        $connection->dropTable('users');
    });
});
