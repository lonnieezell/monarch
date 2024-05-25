<?php

namespace Monarch\Database;

use PDO;
use PDOException;

class MySQLConnection extends Connection implements DatabaseInterface
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

        $query = $this->run("SHOW TABLES LIKE ?", [$table]);
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

        $query = $this->run("SHOW TABLES");
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

        $query = $this->run("DESCRIBE $table");
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
        return array_map(fn ($column) => $column['Field'], $columns);
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
            if ($column['Key'] === 'PRI') {
                return $column['Field'];
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

        $this->run("DROP TABLE $table");
    }

    /**
     * Add a column to a table.
     *
     * Example:
     *  db()->addColumn('users', 'email', 'TEXT NOT NULL');
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
     *  db()->dropColumn('users', 'email');
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
     *  db()->addIndex('users', 'email');
     *
     * @throws PDOException
     */
    public function addIndex(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table ADD INDEX ($column)");
    }

    /**
     * Drop an index from a table.
     *
     * Example:
     *  db()->dropIndex('users', 'email');
     *
     * @throws PDOException
     */
    public function dropIndex(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table DROP INDEX ($column)");
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

        $query = $this->run("SHOW INDEX FROM $table WHERE Column_name = ?", [$column]);
        return (bool) $query->fetchColumn();
    }

    /**
     * Add a foreign key to a table.
     *
     * Example:
     *  db()->addForeignKey('users', 'role_id', 'roles', 'id');
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
     *  db()->dropForeignKey('users', 'role_id');
     *
     * @throws PDOException
     */
    public function dropForeignKey(string $table, string $column): void
    {
        $this->ensureConnection();

        $this->run("ALTER TABLE $table DROP FOREIGN KEY $column");
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

        $query = $this->run("SHOW CREATE TABLE $table");
        $result = $query->fetchColumn();

        return strpos($result, "FOREIGN KEY ($column)") !== false;
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
