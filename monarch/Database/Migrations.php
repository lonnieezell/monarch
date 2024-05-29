<?php

namespace Monarch\Database;

use Closure;
use PDOException;

/**
 * Provides a way to manage database migrations.
 * Migrations are a way to manage database schema changes over time.
 * Migrations are typically used to create new tables, add columns, or modify existing columns.
 * Migrations are typically version controlled and can be run in a specific order.
 *
 * This class provides a way to run, rollback, and start fresh with migrations.
 * Migrations are stored within the 'migrations' table, which is created if it doesn't exist.
 */
class Migrations
{
    private Connection $connection;

    public function __construct(?Connection $connection = null)
    {
        if (!$connection) {
            $connection = db();
        }

        $this->connection = $connection;
    }

    /**
     * Run all pending migrations.
     */
    public function latest(Closure $callback = null): void
    {
        $this->createMigrationsTable();

        $migrations = $this->getWaitingMigrations();

        foreach ($migrations as $migration) {
            $callback && $callback($migration['migration']);
            $this->runMigration($migration['migration'], $migration['batch']);
        }
    }

    /**
     * Rollback the last migration.
     */
    public function rollback(Closure $callback = null): void
    {
        $this->createMigrationsTable();

        $migrations = $this->getLatestMigrationBatch();

        if (!$migrations) {
            return;
        }

        foreach ($migrations as $migration) {
            $callback && $callback($migration['migration']);
            $this->rollbackMigration($migration);
        }

        $this->connection->sql('DELETE FROM migrations
            WHERE batch = ?', [
                $migrations[0]['batch']
            ])
            ->run();
    }

    /**
     * Start fresh by rolling back all migrations.
     */
    public function fresh(): void
    {
        $this->createMigrationsTable();

        $migrations = $this->getCompletedMigrations();

        foreach ($migrations as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    /**
     * Create the migrations table if it doesn't exist.
     */
    private function createMigrationsTable(): void
    {
        $this->connection->sql('CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration TEXT,
            batch INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )')
            ->run();
    }

    /**
     * Returns an array of migration file paths that have not been ran.
     *
     * @throws PDOException
     */
    private function getWaitingMigrations(): array
    {
        $migrations = $this->getCompletedMigrations();
        $ranMigrations = array_column($migrations, 'migration');

        $files = glob('database/migrations/*.php');

        $waitingMigrations = array_filter($files, function ($file) use ($ranMigrations) {
            return !in_array($file, $ranMigrations);
        });

        $lastBatch = count($migrations) > 0
            ? max(array_column($migrations, 'batch'))
            : 0;

        return array_map(function ($migration) use ($lastBatch) {
            return [
                'migration' => $migration,
                'batch' => $lastBatch + 1,
            ];
        }, $waitingMigrations);
    }

    /**
     * Gets all migrations that have previously been ran.
     *
     * @throws PDOException
     */
    private function getCompletedMigrations(): array
    {
        return $this->connection->run('SELECT * FROM migrations ORDER BY id DESC')
            ->fetchAll();
    }

    /**
     * Returns the migrations in the most recent batch that have been ran.
     *
     * @throws PDOException
     */
    private function getLatestMigrationBatch(): ?array
    {
        $migrations = $this->getCompletedMigrations();
        $latestBatch = max(array_column($migrations, 'batch'));

        return array_filter($migrations, function ($migration) use ($latestBatch) {
            return $migration['batch'] === $latestBatch;
        });
    }

    /**
     * Runs a single migration.
     */
    private function runMigration(array $migration, int $batchId): void
    {
        $this->connection->pdo->beginTransaction();

        try {
            $class = require_once $migration['migration'];
            $class->up();

            $this->connection->sql('INSERT INTO migrations (migration, batch, created_at) VALUES (:migration, :batch, NOW())', [
                    ':migration' => $migration['migration'],
                    ':batch' => $batchId,
                ])
                ->run();

            $this->connection->pdo->commit();
        } catch (PDOException $e) {
            $this->connection->pdo->rollback();
            throw $e;
        }
    }

    /**
     * Rollback a migration.
     * @param array $migration
     * @return void
     */
    private function rollbackMigration(array $migration): void
    {
        $this->connection->pdo->beginTransaction();

        try {
            $class = require_once $migration['migration'];
            $class->down();

            $this->connection->pdo->commit();
        } catch (PDOException $e) {
            $this->connection->pdo->rollback();
            throw $e;
        }
    }
}
