<?php

use Monarch\Database\Migrations;
use Monarch\Database\Connection;

describe('Migrations', function () {
    beforeEach(function () {
        $this->migrations = new Migrations(db());
        $this->migrations->setMigrationsPath(TESTPATH .'_support/database/migrations');

        db()->dropTable('migrations');
        db()->dropTable('users');
        db()->dropTable('posts');
    });

    it('should run all pending migrations recursively', function () {
        $this->migrations->latest();

        expect(db()->tableExists('migrations'))->toBeTrue();
        expect(db()->tableExists('users'))->toBeTrue();
        expect(db()->tableExists('posts'))->toBeTrue();
    });

    it('can limit the migrations to run by folder', function () {
        $this->migrations->inDirectory('v1.1')->latest();

        expect(db()->tableExists('migrations'))->toBeTrue();
        expect(db()->tableExists('users'))->toBeFalse();
        expect(db()->tableExists('posts'))->toBeTrue();
    });

    it('can limit the migrations to run by name', function () {
        $this->migrations->only('v1/001_create_users_table.sql')->latest();

        expect(db()->tableExists('migrations'))->toBeTrue();
        expect(db()->tableExists('users'))->toBeTrue();
        expect(db()->tableExists('posts'))->toBeFalse();
    });

    it('should drop all tables and re-run all migrations', function () {
        $this->migrations->latest();

        db()->run('INSERT INTO users (name, email, password) VALUES (?, ?, ?)', [
            'John Doe',
            'johndoe@example.com',
            'secret',
        ]);

        expect(db()->run('SELECT * FROM users')->fetchAll())->toHaveCount(1);

        $this->migrations->fresh();

        expect(db()->tableExists('migrations'))->toBeTrue();
        expect(db()->tableExists('users'))->toBeTrue();

        expect(db()->run('SELECT * FROM users')->fetchAll())->toHaveCount(0);
    });
});
