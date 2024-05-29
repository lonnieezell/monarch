<?php

use Monarch\Database\Migrations;
use Monarch\Database\Connection;

describe('Migrations', function () {
    beforeEach(function () {
        $this->migrations = new Migrations(db());
    });

    it('should create migrations table if it does not exist', function () {
        $this->migrations->latest();

        // Check the table exists in the database
        $tableExists = db()->run(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'"
        )->fetchColumn();
    });

    it('should run all pending migrations', function () {
        // Mock the `getWaitingMigrations` method to return a dummy result
        $this->migrations->shouldReceive('getWaitingMigrations')->andReturn([
            [
                'migration' => 'path/to/migration1.php',
                'batch' => 1,
            ],
            [
                'migration' => 'path/to/migration2.php',
                'batch' => 1,
            ],
        ]);

        // Mock the `runMigration` method to do nothing
        $this->migrations->shouldReceive('runMigration')->andReturnNull();

        // Call the `latest` method
        $this->migrations->latest();

        // Assert that the `runMigration` method was called for each pending migration
        $this->migrations->shouldHaveReceived('runMigration')->twice();
    });

    it('should rollback the last migration', function () {
        // Mock the `getLatestMigrationBatch` method to return a dummy result
        $this->migrations->shouldReceive('getLatestMigrationBatch')->andReturn([
            [
                'migration' => 'path/to/migration1.php',
                'batch' => 1,
            ],
        ]);

        // Mock the `rollbackMigration` method to do nothing
        $this->migrations->shouldReceive('rollbackMigration')->andReturnNull();

        // Mock the `sql` method to return a dummy result
        $this->migrations->getConnection()->shouldReceive('sql')->andReturnSelf();

        // Call the `rollback` method
        $this->migrations->rollback();

        // Assert that the `rollbackMigration` method was called for the last migration
        $this->migrations->shouldHaveReceived('rollbackMigration')->once();

        // Assert that the `sql` method was called with the correct query
        $this->migrations->getConnection()->shouldHaveReceived('sql')->with(
            'DELETE FROM migrations WHERE batch = ?',
            [1]
        );
    });

    it('should rollback all completed migrations', function () {
        // Mock the `getCompletedMigrations` method to return a dummy result
        $this->migrations->shouldReceive('getCompletedMigrations')->andReturn([
            [
                'migration' => 'path/to/migration1.php',
                'batch' => 1,
            ],
            [
                'migration' => 'path/to/migration2.php',
                'batch' => 1,
            ],
        ]);

        // Mock the `rollbackMigration` method to do nothing
        $this->migrations->shouldReceive('rollbackMigration')->andReturnNull();

        // Call the `fresh` method
        $this->migrations->fresh();

        // Assert that the `rollbackMigration` method was called for each completed migration
        $this->migrations->shouldHaveReceived('rollbackMigration')->twice();
    });
});
