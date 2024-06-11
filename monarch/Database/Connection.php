<?php

namespace Monarch\Database;

use Monarch\Database\Drivers\MySQLDriver;
use Monarch\Database\Drivers\PostgreSQLDriver;
use Monarch\Database\Drivers\SQLiteDriver;
use Monarch\Database\Extensions\QueryBuilder;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;
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
    private array $extensions = [];

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
                'sqlite' => SQLiteDriver::class,
                'mysql' => MySQLDriver::class,
                'postgres' => PostgreSQLDriver::class,
                default => throw new RuntimeException('Unsupported database driver: ' . $driver),
            };

            self::$instances[$fingerprint] = new $className();
            self::$instances[$fingerprint]->withConfig($config);
            self::$instances[$fingerprint]->loadExtensions(config('database.extensions') ?? []);
        }

        return self::$instances[$fingerprint];
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
     * Creates instances of all the extensions provided in the configuration.
     */
    public function loadExtensions(array $extensions): void
    {
        foreach ($extensions as $className) {
            if (! class_exists($className)) {
                throw new RuntimeException('Extension not found: ' . $className);
            }

            if (isset($this->extensions[$className])) {
                throw new RuntimeException('Extension already loaded: ' . $className);
            }

            $class = new ReflectionClass($className);

            if (! $class->implementsInterface(ExtensionInterface::class)) {
                throw new RuntimeException('Extension must implement ExtensionInterface: ' . $className);
            }

            /** @var $class ExtensionInterface */
            $className::extend($this);
        }
    }

    /**
     * Returns the registered extensions.
     */
    public function extensions(): array
    {
        return $this->extensions;
    }

    /**
     * Registers a new extension with the connection.
     */
    public function register(string $name, callable $callback): void
    {
        $this->extensions[$name] = $callback;
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
            if (isset($this->extensions['queryBuilder'])) {
                $builder = $this->extensions['queryBuilder']();
                $sql = $builder->toSql();
                $params = $builder->bindings();
                $builder->reset();
            }
        }

        if (empty($sql)) {
            throw new RuntimeException('No query provided');
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
        if (isset($this->pdo)) {
            return;
        }

        $this->connect();
    }

    /**
     * Magic method to allow for chaining of methods on the QueryBuilder object.
     */
    public function __call($name, $arguments)
    {
        if (isset($this->extensions[$name])) {
            return $this->extensions[$name](...$arguments);
        }
    }
}
