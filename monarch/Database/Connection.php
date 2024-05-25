<?php

namespace Monarch\Database;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * Represents a database connection.
 *
 * This class is responsible for creating a new PDO connection based on the
 * configuration provided. It also ensures that only one connection is created
 * per unique configuration.
 *
 * @see https://phpdelusions.net/pdo for a good overview of PDO usage.
 */
class Connection
{
    public ?PDO $pdo;
    protected static array $instances = [];
    protected array $config = [];
    private QueryBuilder $queryBuilder;

    /**
     * Creates a new singleton instance of the database connection.
     * A unique fingerprint is generated based on the configuration array.
     */
    public static function createWithConfig(array $config): static
    {
        $fingerprint = md5(serialize($config));

        if (!isset(self::$instances[$fingerprint])) {
            $driver = $config['driver'] ?? null;

            if ($driver === null) {
                throw new RuntimeException('Database driver is required');
            }

            $className = match ($driver) {
                'sqlite' => SQLiteConnection::class,
                'mysql' => MySQLConnection::class,
                'postgres' => PostgreSQLConnection::class,
                default => throw new RuntimeException('Unsupported database driver: ' . $driver),
            };

            self::$instances[$fingerprint] = new $className();
            self::$instances[$fingerprint]->withConfig($config);
        }

        return self::$instances[$fingerprint];
    }

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Connects to the database using the provided configuration.
     *
     * @throws PDOException
     */
    public function withConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Executes a query and returns the PDOStatement object.
     *
     * If no parameters are provided, the query is executed directly.
     * If parameters are provided, the query is prepared and executed with the parameters.
     * If a QueryBuilder instance is provided, the query is executed with the QueryBuilder's SQL and bindings.
     *
     * Example:
     *   db()->query('SELECT * FROM users WHERE id = :id', ['id' => 1])->fetchAll();
     *
     * @throws PDOException
     */
    public function run(string|QueryBuilder $sql = null, ?array $params = null): PDOStatement
    {
        $this->ensureConnection();

        if ($sql instanceof QueryBuilder) {
            $params = $sql->bindings();
            $sql = $sql->toSql();
        }

        if (empty($sql)) {
            $sql = $this->queryBuilder->toSql();
            $params = $this->queryBuilder->bindings();
            $this->queryBuilder->reset();
        }

        if ($params === null || $params === []) {
            return $this->pdo->query($sql);
        }

        $statement = $this->pdo->prepare($sql);
        if (! $statement->execute($params)) {
            throw new PDOException('Failed to execute query');
        }

        return $statement;
    }

    /**
     * Closes the connection to the database.
     */
    public function close(): void
    {
        $this->pdo = null;
    }

    /**
     * Checks that a connection exists and creates one if it does not.
     *
     * @throws RuntimeException
     */
    private function ensureConnection(): void
    {
        if (!isset($this->pdo)) {
            $this->connect();
        }
    }

    /**
     * Builds the DSN for the PDO connection.
     *
     * @throws RuntimeException
     */
    private function buildDSN(string $driver): string
    {
        if (empty($this->config['database'])) {
            throw new RuntimeException('Database name is required');
        }

        // SQLite
        if ($driver === 'sqlite') {
            return sprintf('sqlite:%s', $this->config['database']);
        }

        if (empty($this->config['charset'])) {
            throw new RuntimeException('Charset is required');
        }

        // MySQL
        if ($driver === 'mysql') {
            return sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $driver,
                $this->config['host'] ?? 'localhost',
                $this->config['port'] ?? 3306,
                $this->config['database'],
                $this->config['charset'],
            );
        }

        // PostgreSQL
        return sprintf(
            '%s:host=%s;port=%s;dbname=%s;sslmode=%s',
            $driver,
            $this->config['host'] ?? 'localhost',
            $this->config['port'] ?? 3306,
            $this->config['database'],
            $this->config['sslmode'],
        );
    }

    /**
     * Magic method to allow for chaining of methods on the QueryBuilder object.
     */
    public function __call($name, $arguments): static
    {
        if (method_exists($this->queryBuilder, $name)) {
            $this->queryBuilder->$name(...$arguments);
        }

        return $this;
    }
}
