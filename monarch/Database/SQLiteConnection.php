<?php

namespace Monarch\Database;

use PDO;
use PDOException;

class SQLiteConnection extends Connection implements DatabaseInterface
{
    /**
     * Connect to the database.
     *
     * Example:
     * db()->connect(['database' => ':memory:']);
     *
     * @throws PDOException
     */
    public function connect(?array $options = null): PDO
    {
        if (empty($options)) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
        }

        $database = $this->config['database'] ?? null;

        if ($database === null) {
            throw new PDOException('Database file is required');
        }

        $this->pdo = new PDO("sqlite:{$database}", null, null, $options);

        return $this->pdo;
    }

    /**
     * Checks if a table exists in the database.
     *
     * Example:
     * db()->tableExists('users');
     *
     * @throws PDOException
     */
    public function tableExists(string $table): bool
    {
        $this->ensureConnection();

        $query = $this->run("SELECT name FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
        return (bool) $query->fetchColumn();
    }

    /**
     * Get a list of all tables in the database.
     *
     * Example:
     * db()->tables();
     *
     * @throws PDOException
     */
    public function tables(): array
    {
        $this->ensureConnection();

        $query = $this->run("SELECT name FROM sqlite_master WHERE type='table'");
        return $query->fetchAll(PDO::FETCH_COLUMN) ?? [];
    }

    /**
     * Get a list of all columns in a table.
     *
     * Example:
     * db()->columns('users');
     *
     * @throws PDOException
     */
    public function columns(string $table): array
    {
        $this->ensureConnection();

        $query = $this->run("PRAGMA table_info($table)");
        return $query->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Get the column names for a table.
     *
     * Example:
     * db()->columnNames('users');
     *
     * @throws PDOException
     */
    public function columnNames(string $table): array
    {
        $columns = $this->columns($table);
        return array_map(fn ($column) => $column['name'], $columns);
    }

    /**
     * Get the primary key for a table.
     *
     * Example:
     * db()->primaryKey('users');
     *
     * @throws PDOException
     */
    public function primaryKey(string $table): ?string
    {
        $columns = $this->columns($table);

        foreach ($columns as $column) {
            if ($column['pk'] === 1) {
                return $column['name'];
            }
        }

        return null;
    }

    /**
     * Create a new table in the database.
     *
     * Example:
     *  db()->createTable('users', [
     *      'id INTEGER PRIMARY KEY AUTOINCREMENT',
     *      'name TEXT NOT NULL',
     *      'email TEXT NOT NULL',
     *  ]);
     *
     * @throws PDOException
     */
    public function createTable(string $table, array $columns): void
    {
        $this->ensureConnection();

        $sql = "CREATE TABLE $table (";
        $sql .= implode(', ', $columns);
        $sql .= ')';

        $this->run($sql);
    }

    /**
     * Drop a table from the database.
     *
     * Example:
     * db()->dropTable('users');
     *
     * @throws PDOException
     */
    public function dropTable(string $table): void
    {
        $this->ensureConnection();

        $this->run("DROP TABLE $table");
    }

    /**
     * Add a column to a table.
     *
     * Example:
     * db()->addColumn('users', 'email', 'TEXT NOT NULL');
     *
     * @throws PDOException
     */
    public function addColumn(string $table, string $column, string $type): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table ADD COLUMN $column $type");
    }

    /**
     * Drop a column from a table.
     *
     * Example:
     * db()->dropColumn('users', 'email');
     *
     * @throws PDOException
     */
    public function dropColumn(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table DROP COLUMN $column");
    }

    /**
     * Add an index to a table.
     *
     * Example:
     * db()->addIndex('users', 'email');
     *
     * @throws PDOException
     */
    public function addIndex(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("CREATE INDEX {$table}_{$column}_index ON $table ($column)");
    }

    /**
     * Drop an index from a table.
     *
     * Example:
     * db()->dropIndex('users', 'email');
     *
     * @throws PDOException
     */
    public function dropIndex(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("DROP INDEX {$table}_{$column}_index");
    }

    /**
     * Check if an index exists on a table.
     *
     * Example:
     * db()->indexExists('users', 'email');
     *
     * @throws PDOException
     */
    public function indexExists(string $table, string $column): bool
    {
        $this->ensureConnection();

        $query = $this->run("PRAGMA index_list($table)");
        $indexes = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($indexes as $index) {
            if ($index['name'] === "{$table}_{$column}_index") {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a foreign key to a table.
     *
     * Example:
     * db()->addForeignKey('users', 'role_id', 'roles', 'id');
     *
     * @throws PDOException
     */
    public function addForeignKey(string $table, string $column, string $foreignTable, string $foreignColumn): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table ADD FOREIGN KEY ($column) REFERENCES $foreignTable($foreignColumn)");
    }

    /**
     * Drop a foreign key from a table.
     *
     * Example:
     * db()->dropForeignKey('users', 'role_id');
     *
     * @throws PDOException
     */
    public function dropForeignKey(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table DROP FOREIGN KEY $table{$column}_foreign");
    }

    /**
     * Check if a foreign key exists on a table.
     *
     * Example:
     * db()->foreignKeyExists('users', 'role_id');
     *
     * @throws PDOException
     */
    public function foreignKeyExists(string $table, string $column): bool
    {
        $this->ensureConnection();

        $query = $this->run("PRAGMA foreign_key_list($table)");
        $foreignKeys = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey['from'] === $column) {
                return true;
            }
        }

        return false;
    }

    /**
     * Disables foreign key constraints.
     *
     * Example:
     * db()->disableForeignKeys();
     *
     * @throws PDOException
     */
    public function disableForeignKeys(): void
    {
        $this->ensureConnection();

        $this->run('PRAGMA foreign_keys = OFF');
    }

    /**
     * Enables foreign key constraints.
     *
     * Example:
     * db()->enableForeignKeys();
     *
     * @throws PDOException
     */
    public function enableForeignKeys(): void
    {
        $this->ensureConnection();

        $this->run('PRAGMA foreign_keys = ON');
    }
}
