<?php

namespace Monarch\Database\Drivers;

use DateTimeInterface;
use Monarch\Database\Connection;
use Monarch\Database\DriverInterface;
use PDO;
use PDOException;

class SQLiteDriver extends Connection implements DriverInterface
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
     * Returns the correct date/time format for the database.
     */
    public function formatDateTime(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
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

        $query = $this->run("SELECT name, type = 'view' as view
			FROM sqlite_master
			WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%'
            ORDER BY name");
        $rows = $query->fetchAll() ?? [];

        $tables = [];

        foreach ($rows as $row) {
            $tables[] = [
                'name' => $row->name,
                'view' => (bool)$row->view,
            ];
        }

        return $tables;
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
     * Get a list of all indexes in a table.
     *
     * Example:
     * $indexes = db()->indexes('users');
     *
     * returns: [
     *    [
     *      'name' => 'PRIMARY',
     *      'unique' => true,
     *      'primary' => true,
     *      'columns' => ['id']
     *   ],
     * ];
     */
    public function indexes(string $table): array
    {
        $this->ensureConnection();

        $query = $this->run("PRAGMA index_list($table)");
        $indexes = $query->fetchAll(PDO::FETCH_ASSOC);

        $list = [];

        foreach ($indexes as $index) {
            $query = $this->run("PRAGMA index_info({$index['name']})");
            $columns = $query->fetchAll(PDO::FETCH_ASSOC);

            $list[] = [
                'name' => $index['name'],
                'unique' => $index['unique'] === 1,
                'primary' => $index['origin'] === 'pk',
                'columns' => array_map(fn ($column) => $column['name'], $columns),
            ];
        }

        return $list;
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
     * Get a list of all foreign keys in a table.
     *
     * Example:
     * $foreignKeys = db()->foreignKeys('users');
     *
     * returns: [
     *   [
     *      'name' => 'users_role_id_foreign',
     *      'local' => 'role_id',
     *      'table' => 'roles',
     *      'foreign' => 'id',
     *  ],
     * ];
     */
    public function foreignKeys(string $table): array
    {
        $this->ensureConnection();

        $query = $this->run("PRAGMA foreign_key_list($table)");
        $foreignKeys = $query->fetchAll(PDO::FETCH_ASSOC);

        $list = [];

        foreach ($foreignKeys as $foreignKey) {
            $list[] = [
                'name' => $foreignKey['id'],
                'local' => $foreignKey['from'],
                'table' => $foreignKey['table'],
                'foreign' => $foreignKey['to'],
            ];
        }

        return $list;
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
