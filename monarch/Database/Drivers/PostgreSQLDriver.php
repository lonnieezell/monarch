<?php

namespace Monarch\Database\Drivers;

use DateTimeInterface;
use Monarch\Database\Connection;
use Monarch\Database\DriverInterface;
use PDO;
use PDOException;

class PostgreSQLDriver extends Connection implements DriverInterface
{
    /**
     * Connect to the database.
     *
     * Example:
     * db()->connect([
     *      'host' => 'localhost',
     *      'port' => '5432',
     *      'user' =>
     *      'password' => 'password',
     *      'database' => 'database',
     *      'charset' => 'utf8',
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
        $port = $this->config['port'] ?? '5432';
        $database = $this->config['database'] ?? null;
        $username = $this->config['user'] ?? null;
        $password = $this->config['password'] ?? null;
        $charset = $this->config['charset'] ?? 'utf8';

        if ($database === null) {
            throw new PDOException('Database name is required');
        }

        if ($username === null) {
            throw new PDOException('Database username is required');
        }

        if ($password === null) {
            throw new PDOException('Database password is required');
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$database};charset={$charset}";

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
     * db()->tableExists('users');
     *
     * @throws PDOException
     */
    public function tableExists(string $table): bool
    {
        $this->ensureConnection();

        $query = $this->run("SELECT to_regclass(?)", [$table]);
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

        $query = $this->run("SELECT DISTINCT ON (c.relname)
				c.relname::varchar AS name,
				c.relkind IN ('v', 'm') AS view,
				n.nspname::varchar || '.' || c.relname::varchar AS \"fullName\"
			FROM
				pg_catalog.pg_class AS c
				JOIN pg_catalog.pg_namespace AS n ON n.oid = c.relnamespace
			WHERE
				c.relkind IN ('r', 'v', 'm', 'p')
				AND n.nspname = ANY (pg_catalog.current_schemas(FALSE))
			ORDER BY
				c.relname");
        $rows = $query->fetchAll(PDO::FETCH_COLUMN) ?? [];

        $tables = [];

        foreach ($rows as $row) {
            $tables[] = (array) $row;
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

        $query = $this->run("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ?", [$table]);
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
        $this->ensureConnection();

        $query = $this->run("SELECT column_name FROM information_schema.columns WHERE table_name = ?", [$table]);
        return $query->fetchAll(PDO::FETCH_COLUMN) ?? [];
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
        $this->ensureConnection();

        $query = $this->run("SELECT column_name FROM information_schema.key_column_usage WHERE table_name = ? AND constraint_name = 'PRIMARY'", [$table]);
        return $query->fetchColumn();
    }

    /**
     * Create a new table in the database.
     *
     * Example:
     * db()->createTable('users', [
     *     'id' => 'serial PRIMARY KEY',
     *     'name' => 'varchar(255)',
     * ]);
     *
     * @throws PDOException
     */
    public function createTable(string $table, array $columns): void
    {
        $this->ensureConnection();

        $sql = "CREATE TABLE $table (";
        $sql .= implode(', ', array_map(fn ($column, $type) => "$column $type", array_keys($columns), $columns));
        $sql .= ")";

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

        $query = $this->run("SELECT indexname, indisunique, indisprimary, indkey FROM pg_indexes WHERE tablename = ?", [$table]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC) ?? [];

        $indexes = [];

        foreach ($rows as $row) {
            $columns = $this->run("SELECT attname FROM pg_attribute WHERE attrelid = ? AND attnum = ANY (?)", [$table, $row['indkey']])->fetchAll(PDO::FETCH_COLUMN) ?? [];

            $indexes[] = [
                'name' => $row['indexname'],
                'unique' => $row['indisunique'],
                'primary' => $row['indisprimary'],
                'columns' => $columns,
            ];
        }

        return $indexes;
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

        $query = $this->run("SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexdef LIKE ?", [$table, "%($column)"]);
        return (bool) $query->fetchColumn();
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

        $query = $this->run("SELECT conname, attname, confrelid::regclass, conkey FROM pg_constraint JOIN pg_attribute ON attrelid = conrelid WHERE conrelid = ?", [$table]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC) ?? [];

        $foreignKeys = [];

        foreach ($rows as $row) {
            $columns = $this->run("SELECT attname FROM pg_attribute WHERE attrelid = ? AND attnum = ANY (?)", [$table, $row['conkey']])->fetchAll(PDO::FETCH_COLUMN) ?? [];

            $foreignKeys[] = [
                'name' => $row['conname'],
                'local' => $row['attname'],
                'table' => $row['confrelid'],
                'foreign' => $columns[0],
            ];
        }

        return $foreignKeys;
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

        $query = $this->run("SELECT constraint_name FROM information_schema.key_column_usage WHERE table_name = ? AND column_name = ?", [$table, $column]);
        return (bool) $query->fetchColumn();
    }

    /**
     * Enable foreign key constraints.
     *
     * Example:
     * db()->enableForeignKeys();
     *
     * @throws PDOException
     */
    public function disableForeignKeys(): void
    {
        $this->ensureConnection();

        $this->run("SET session_replication_role = 'replica'");
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

        $this->run("SET session_replication_role = 'origin'");
    }
}
