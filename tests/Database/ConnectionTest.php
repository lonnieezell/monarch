<?php

use Monarch\Database\Connection;
use Monarch\Database\SQLiteConnection;

use function PHPUnit\Framework\assertFalse;

beforeEach(function () {
    $config = [
        'driver' => 'sqlite',
        'database' => ':memory:',
    ];

    $this->connection = Connection::createWithConfig($config);
});

describe('Database Connection', function () {
    test('create with config', function () {
        $config = [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ];

        $connection = Connection::createWithConfig($config);
        expect($connection)->toBeInstanceOf(SQLiteConnection::class);
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

    test('table exists', function () {
        expect($this->connection->tableExists('users'))->toBeFalse();

        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        expect($this->connection->tableExists('users'))->toBeTrue();

        $this->connection->dropTable('users');
    });

    test('tables', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        $this->connection->createTable('posts', [
            'id integer primary key',
            'title text allowNull',
        ]);

        $tables = $this->connection->tables();

        expect($tables)->toBeArray();
        expect($tables)->toContain('users');
        expect($tables)->toContain('posts');

        $this->connection->dropTable('users');
        $this->connection->dropTable('posts');
    });

    test('columns', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        $columns = $this->connection->columns('users');

        expect($columns)->toBeArray();
        expect($columns[0]['name'])->toBe('id');
        expect(strtolower($columns[0]['type']))->toBe('integer');

        $this->connection->dropTable('users');
    });

    test('columnNames', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        $columns = $this->connection->columnNames('users');

        expect($columns[0])->toBe('id');
        expect($columns[1])->toBe('name');

        $this->connection->dropTable('users');
    });

    test('primaryKey', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        expect($this->connection->primaryKey('users'))->toBe('id');

        $this->connection->dropTable('users');
    });

    test('addColumn', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        $this->connection->addColumn('users', 'email', 'text not null');

        $columns = $this->connection->columns('users');

        expect($columns)->toBeArray();
        expect($columns[2]['name'])->toBe('email');
        expect(strtolower($columns[2]['type']))->toBe('text');

        $this->connection->dropTable('users');
    });

    test('dropColumn', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
            'email text not null',
        ]);

        $this->connection->dropColumn('users', 'email');

        $columns = $this->connection->columns('users');

        expect($columns)->toBeArray();
        expect($columns)->not()->toContain('email');

        $this->connection->dropTable('users');
    });

    test('addIndex', function () {
        $this->connection->createTable('users', [
            'id integer primary key',
            'name text allowNull',
        ]);

        $this->connection->addIndex('users', 'name');

        $columns = $this->connection->columns('users');

        expect($columns)->toBeArray();
        expect($columns[1]['name'])->toBe('name');
        expect($columns[1]['pk'])->toBe(0);

        $this->connection->dropTable('users');
    });
});
