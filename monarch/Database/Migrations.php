<?php

namespace Monarch\Database;

use Closure;
use PDOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

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
    private string $basePath = ROOTPATH .'database/migrations';
    private string $subDirectory = '';
    private string $onlyMigration = '';

    public function __construct(?Connection $connection = null)
    {
        if (!$connection) {
            $connection = db();
        }

        $this->connection = $connection;
    }

    /**
     * Set the path to the migrations directory.
     */
    public function setMigrationsPath(string $path): self
    {
        $this->basePath = rtrim($path, ' /') .'/';

        return $this;
    }

    /**
     * Only run a single migration file.
     */
    public function only(string $migration): self
    {
        $this->onlyMigration = $migration;

        return $this;
    }

    /**
     * Set a sub-directory within the migrations directory.
     */
    public function inDirectory(string $dir): self
    {
        $this->subDirectory = rtrim($dir, ' /') .'/';

        return $this;
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
     * Drop all tables in the database and re-run all migrations.
     */
    public function fresh(): void
    {
        $this->createMigrationsTable();

        $tables = $this->connection->tables();

        foreach ($tables as $table) {
            $this->connection->dropTable($table['name']);
        }

        $this->latest();
    }

    /**
     * Create the migrations table if it doesn't exist.
     */
    private function createMigrationsTable(): void
    {
        $this->connection->sql('CREATE TABLE IF NOT EXISTS migrations (
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
        $onlyMigration = $this->basePath . $this->onlyMigration;

        $startPath = $this->subDirectory
            ? "{$this->basePath}{$this->subDirectory}"
            : $this->basePath;

        // Recursively search for migration files.
        $dir = new RecursiveDirectoryIterator($startPath);
        $iterator = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($iterator, '/^.+\.sql$/i', RecursiveRegexIterator::GET_MATCH);
        $waitingMigrations = [];

        foreach ($files as $file) {
            if (in_array($file[0], $ranMigrations)) {
                continue;
            }
            if ($this->onlyMigration && $onlyMigration !== $file[0]) {
                continue;
            }

            $waitingMigrations[] = $file[0];
        }

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
        return $this->connection->run('SELECT * FROM migrations ORDER BY created_at DESC')
            ->fetchAll();
    }

    /**
     * Runs a single migration.
     */
    private function runMigration(string $migration, int $batchId): void
    {
        $sql = file_get_contents($migration);
        $this->connection->run($sql);

        $this->connection->sql('INSERT INTO migrations (migration, batch, created_at) VALUES (:migration, :batch, NOW())', [
                ':migration' => $migration,
                ':batch' => $batchId,
            ])
            ->run();
    }
}
