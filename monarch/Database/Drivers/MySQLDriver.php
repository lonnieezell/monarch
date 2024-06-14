<?php

namespace Monarch\Database\Drivers;

use DateTimeInterface;
use Monarch\Database\Connection;
use Monarch\Database\DriverInterface;
use PDO;
use PDOException;

class MySQLDriver extends Connection implements DriverInterface
{
    /**
     * Connect to the database.
     *
     * Example:
     *  db()->connect([
     *      'host' => 'localhost',
     *      'port' => '3306',
     *      'user' => 'root',
     *      'password' => 'root',
     *      'database' => 'database',
     *  ]);
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

        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? '3306';
        $database = $this->config['database'] ?? null;
        $username = $this->config['user'] ?? null;
        $password = $this->config['password'] ?? null;
        $charset = $this->config['charset'] ?? 'utf8mb4';

        if ($database === null) {
            throw new PDOException('Database name is required');
        }

        if ($username === null) {
            throw new PDOException('Database username is required');
        }

        if ($password === null) {
            throw new PDOException('Database password is required');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        $this->pdo = new PDO($dsn, $username, $password, $options);

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
     *  db()->tableExists('users');
     *
     * @throws PDOException
     */
    public function tableExists(string $table): bool
    {
        $this->ensureConnection();

        $tables = $this->tables();
        return in_array($table, array_column($tables, 'name'));
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

        $query = $this->run("SHOW FULL TABLES");
        $rows = $query->fetchAll(PDO::FETCH_COLUMN) ?? [];

        $tables = [];

        foreach ($rows as $row) {
            $tables[] = [
                'name' => is_string($row) ? $row : $row[0],
                'view' => is_string($row) ? false : ($row[1] ?? null) === 'VIEW',
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

        $query = $this->run("SHOW FULL COLUMNS FROM $table");
        $rows = $query->fetchAll(PDO::FETCH_ASSOC) ?? [];
        $columns = [];

        // Ensure all key names are lowercase
        $rows = array_map('array_change_key_case', $rows);

        foreach ($rows as $key => $row) {
            $pair = explode('(', $row['type']);

            $columns[] = [
                'name' => $row['field'],
                'table' => $table,
                'type' => $row['type'],
                'nativetype' => strtoupper($pair[0]),
                'size' => isset($pair[1]) ? (int) $pair[1] : null,
                'nullable' => $row['null'] === 'YES',
                'default' => $row['default'],
                'autoincrement' => $row['extra'] === 'auto_increment',
                'primary' => $row['key'] === 'PRI',
                'vendor' => $row,
            ];
        }

        return $columns;
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
     *  db()->primaryKey('users');
     *
     * @throws PDOException
     */
    public function primaryKey(string $table): ?string
    {
        $columns = $this->columns($table);

        foreach ($columns as $column) {
            if ($column['primary'] === true) {
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
     *      'id INT AUTO_INCREMENT PRIMARY KEY',
     *      'name VARCHAR(255) NOT NULL',
     *      'email VARCHAR(255) NOT NULL',
     *  ]);
     *
     * @throws PDOException
     */
    public function createTable(string $table, array $columns): void
    {
        $this->ensureConnection();

        $sql = "CREATE TABLE $table (";
        $sql .= implode(', ', $columns);
        $sql .= ")";

        $this->run($sql);
    }

    /**
     * Drop a table from the database.
     *
     * Example:
     *  db()->dropTable('users');
     *
     * @throws PDOException
     */
    public function dropTable(string $table): void
    {
        $this->ensureConnection();

        $this->disableForeignKeys();
        $this->run("DROP TABLE IF EXISTS $table");
        $this->enableForeignKeys();
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

        $query = $this->run("SHOW INDEX FROM $table");
        $rows = $query->fetchAll(PDO::FETCH_ASSOC) ?? [];

        $indexes = [];

        // Ensure all key names are lowercase
        $rows = array_map('array_change_key_case', $rows);

        foreach ($rows as $row) {
            $name = $row['key_name'];
            $column = $row['column_name'];

            if (! isset($indexes[$name])) {
                $indexes[$name] = [
                    'name' => $name,
                    'unique' => ! (bool)$row['non_unique'],
                    'primary' => $name === 'PRIMARY',
                    'columns' => [
                        ($row['seq_in_index'] - 1) => $column,
                    ],
                ];
            }
        }

        return $indexes;
    }

    /**
     * Check if an index exists on a table.
     *
     * Example:
     *  db()->indexExists('users', 'email');
     *
     * @throws PDOException
     */
    public function indexExists(string $table, string $column): bool
    {
        $this->ensureConnection();

        $indexes = $this->indexes($table);

        foreach ($indexes as $index) {
            if (in_array($column, $index['columns'])) {
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

        $query = $this->run(<<<X
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IS NOT NULL
                AND TABLE_NAME = ?
            X, [$table]);

        $rows = $query->fetchAll(PDO::FETCH_ASSOC) ?? [];

        $keys = [];

        foreach ($rows as $row) {
            $keys[] = [
                'name' => $row['CONSTRAINT_NAME'],
                'local' => $row['COLUMN_NAME'],
                'table' => $row['REFERENCED_TABLE_NAME'],
                'foreign' => $row['REFERENCED_COLUMN_NAME'],
            ];
        }

        return $keys;
    }

    /**
     * Check if a foreign key exists on a table.
     *
     * Example:
     *  db()->foreignKeyExists('users', 'role_id');
     *
     * @throws PDOException
     */
    public function foreignKeyExists(string $table, string $column): bool
    {
        $this->ensureConnection();

        $keys = $this->foreignKeys($table);

        foreach ($keys as $key) {
            if ($key['local'] === $column) {
                return true;
            }
        }

        return false;
    }

    /**
     * Disable foreign key constraints.
     *
     * Example:
     * db()->disableForeignKeys();
     *
     * @throws PDOException
     */
    public function disableForeignKeys(): void
    {
        $this->ensureConnection();

        $this->run("SET FOREIGN_KEY_CHECKS = 0");
    }

    /**
     * Enable foreign key constraints.
     *
     * Example:
     * db()->enableForeignKeys();
     *
     * @throws PDOException
     */
    public function enableForeignKeys(): void
    {
        $this->ensureConnection();

        $this->run("SET FOREIGN_KEY_CHECKS = 1");
    }
}
