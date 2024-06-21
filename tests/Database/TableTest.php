<?php

use Monarch\Database\Connection;
use Monarch\Helpers\Str;

beforeEach(function () {
    db()->dropTable('migrations');
    db()->dropTable('users');
    db()->dropTable('posts');
});

describe('Database Tables', function () {
    test('can tell if table exists', function () {
        db()->run('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');

        expect(db()->tableExists('users'))->toBeTrue();
        expect(db()->tableExists('posts'))->toBeFalse();
    });

    test('can list tables with no tables', function () {
        $tables = db()->tables();

        expect($tables)->toBeArray();
        expect($tables)->toBeEmpty();
    });

    test('can list tables', function () {
        db()->run('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        db()->run('CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT)');

        $expected = [
            ['name' => 'posts', 'view' => false],
            ['name' => 'users', 'view' => false],
        ];
        $tables = db()->tables();

        expect($tables)->toContain($expected[0]);
        expect($tables)->toContain($expected[1]);
    });

    test('can create a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        expect(db()->tableExists('users'))->toBeTrue();
    });

    test('can drop a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        expect(db()->tableExists('users'))->toBeTrue();

        db()->dropTable('users');

        expect(db()->tableExists('users'))->toBeFalse();
    });

    test('dropTable with no table', function () {
        db()->dropTable('users');

        expect(db()->tableExists('users'))->toBeFalse();
    });

    test('can get a list of columns in a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        $columns = db()->columns('users');

        expect($columns)->toBeArray()->toHaveLength(3);
        expect($columns[0]['name'])->toBe('id');
        expect(Str::like($columns[0]['type'], 'int%'))->toBeTrue();
        expect($columns[0]['primary'])->toBe(true);

        expect($columns[1]['name'])->toBe('name');
        expect($columns[1]['type'])->toBe('text');
        expect($columns[1]['primary'])->toBe(false);

        expect($columns[2]['name'])->toBe('email');
        expect($columns[2]['type'])->toBe('text');
        expect($columns[2]['primary'])->toBe(false);
    });

    test('can get a list of column names in a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        $columns = db()->columnNames('users');

        expect($columns)->toBeArray()->toHaveLength(3);
        expect($columns)->toContain('id');
        expect($columns)->toContain('name');
        expect($columns)->toContain('email');
    });

    test('can get the primary key for a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        expect(db()->primaryKey('users'))->toBe('id');
    });

    test('can get the primary key for a table with no primary key', function () {
        db()->createTable('users', [
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        expect(db()->primaryKey('users'))->toBeNull();
    });

    test('can get a list of indexes in a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name VARCHAR(255) NOT NULL',
            'email VARCHAR(255) NOT NULL',
        ]);

        db()->run('CREATE INDEX users_name ON users (name)');

        $indexes = db()->indexes('users');

        expect($indexes)->toBeArray()->toHaveLength(2);

        $index = array_pop($indexes);
        expect($index['name'])->toBe('users_name');
        expect($index['unique'])->toBeFalse();
        expect($index['columns'])->toBe(['name']);
    });

    test('can check if an index exists', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name VARCHAR(255) NOT NULL',
            'email VARCHAR(255) NOT NULL',
        ]);

        db()->run('CREATE INDEX users_name ON users (name)');

        expect(db()->indexExists('users', 'name'))->toBeTrue();
        expect(db()->indexExists('users', 'email'))->toBeFalse();
    });

    test('can get a list of foreign keys in a table', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        db()->createTable('posts', [
            'id INTEGER PRIMARY KEY',
            'user_id INTEGER',
            'title TEXT NOT NULL',
            'FOREIGN KEY(user_id) REFERENCES users(id)',
        ]);

        $foreignKeys = db()->foreignKeys('posts');

        expect($foreignKeys)->toBeArray()->toHaveLength(1);
        expect($foreignKeys[0]['table'])->toBe('users');
        expect($foreignKeys[0]['local'])->toBe('user_id');
        expect($foreignKeys[0]['foreign'])->toBe('id');
    });

    test('can get a list of foreign keys in a table with no foreign keys', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        db()->createTable('posts', [
            'id INTEGER PRIMARY KEY',
            'title TEXT NOT NULL',
        ]);

        $foreignKeys = db()->foreignKeys('posts');

        expect($foreignKeys)->toBeArray()->toBeEmpty();
    });

    test('can check if a foreign key exists', function () {
        db()->createTable('users', [
            'id INTEGER PRIMARY KEY',
            'name TEXT NOT NULL',
            'email TEXT NOT NULL',
        ]);

        db()->createTable('posts', [
            'id INTEGER PRIMARY KEY',
            'user_id INTEGER',
            'title TEXT NOT NULL',
            'FOREIGN KEY(user_id) REFERENCES users(id)',
        ]);

        expect(db()->foreignKeyExists('posts', 'user_id'))->toBeTrue();
        expect(db()->foreignKeyExists('posts', 'user_email'))->toBeFalse();
    });
});
