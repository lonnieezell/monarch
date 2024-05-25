<?php

namespace Monarch\Database;

use PDO;
use PDOException;

class PostgreSQLConnection extends Connection implements DatabaseInterface
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

        $query = $this->run("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");
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
     * Add a column to a table.
     *
     * Example:
     * db()->addColumn('users', 'email', 'varchar(255)');
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

        $this->run("CREATE INDEX ON $table ($column)");
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

        $this->run("DROP INDEX ON $table ($column)");
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

        $this->run("ALTER TABLE $table ADD CONSTRAINT fk_{$table}_{$column} FOREIGN KEY ($column) REFERENCES $foreignTable($foreignColumn)");
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

        $this->run("ALTER TABLE $table DROP CONSTRAINT fk_{$table}_{$column}");
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
