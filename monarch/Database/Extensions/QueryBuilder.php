<?php

namespace Monarch\Database\Extensions;

use BadMethodCallException;
use Monarch\Database\Connection;
use Monarch\Database\ExtensionInterface;

class QueryBuilder implements ExtensionInterface
{
    private array $lines = [];
    private array $bindings = [];
    private Connection $connection;
    private array $extensions = [];
    private static ?QueryBuilder $instance = null;

    /**
     * Returns a singleton instance of the QueryBuilder.
     */
    public static function instance(): QueryBuilder
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register our extension with the connection.
     */
    public static function extend(Connection $connection): void
    {
        $connection->register('sql', function ($sql, $bindings = null) use ($connection) {
            return QueryBuilder::instance()
                ->withConnection($connection)
                ->sql($sql, $bindings);
        });
        $connection->register('queryBuilder', fn () => QueryBuilder::instance());
    }

    /**
     * Sets the connection for the QueryBuilder.
     */
    public function withConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * The starting point for a new query when using the QueryBuilder.
     *
     * Examples:
     * db()->sql('SELECT * FROM users')->get();
     * db()->sql('INSERT INTO users (name) VALUES (?)', ['John Doe'])->run();
     */
    public function sql(string $sql, ?array $bindings = null): self
    {
        $this->lines[] = $sql;

        if ($bindings) {
            $this->bindings = array_merge($this->bindings, $bindings);
        }

        return $this;
    }

    /**
     * Adds an additional chunk of SQL to the query.
     * This can be used to add WHERE clauses, JOINs, etc.
     * The SQL will be concatenated to the existing query.
     * Bindings can be provided as an array.
     *
     * Example:
     * db()->sql('SELECT * FROM users')
     *      ->concat('WHERE id = ?', [1])
     *      ->run();
     */
    public function concat(string $sql, ?array $bindings = null): self
    {
        $this->lines[] = $sql;

        if ($bindings) {
            $this->bindings = array_merge($this->bindings, $bindings);
        }

        return $this;
    }

    /**
     * Adds a chunk of SQL to the query only if the condition is true.
     *
     * Example:
     * db()->sql('SELECT * FROM users')
     *     ->when($id, 'WHERE id = ?', [$id])
     *     ->run();
     */
    public function when(bool $condition, string $sql, ?array $bindings = null): self
    {
        if ($condition) {
            $this->lines[] = $sql;

            if ($bindings) {
                $this->bindings = array_merge($this->bindings, $bindings);
            }
        }

        return $this;
    }

    /**
     * Adds a chunk of SQL to the query only if the condition is false.
     *
     * Example:
     * db()->sql('SELECT * FROM users')
     *    ->whenNot($id, 'WHERE id = ?', [$id])
     *    ->run();
     */
    public function whenNot(bool $condition, string $sql, ?array $bindings = null): self
    {
        return $this->when(! $condition, $sql, $bindings);
    }

    /**
     * Allows the user to loop over an array and apply a callback to each item.
     * The callback receives the item and the QueryBuilder instance.
     *
     * Example:
     * db()->sql('SELECT * FROM users')
     *    ->each($ids, fn($id, $query) => $query->concat('WHERE id = ?', [$id]))
     */
    public function each(array $items, callable $callback): self
    {
        foreach ($items as $index => $item) {
            $callback($item, $this, $index);
        }

        return $this;
    }

    /**
     * Returns the combined query string.
     */
    public function toSql(): string
    {
        return implode(' ', $this->lines);
    }

    /**
     * Returns the current bindings array.
     */
    public function bindings(): array
    {
        return $this->bindings;
    }

    /**
     * Returns the query string with the bindings replaced.
     * @return string
     */
    public function toString(): string
    {
        $sql = $this->toSql();
        $bindings = $this->bindings();

        foreach ($bindings as $key => $value) {
            $sql = is_string($key)
                ? preg_replace("/:{$key}/", "'$value'", $sql, 1)
                : preg_replace('/\?/', "'$value'", $sql, 1);
        }

        return $sql;
    }

    /**
     * Resets the query builder.
     */
    public function reset(): void
    {
        $this->lines = [];
        $this->bindings = [];
    }

    /**
     * If the method doesn't exist, then check the extensions registered.
     * If still not found, return control back to the connection.
     */
    public function __call($method, $args)
    {
        if (isset($this->extensions[$method])) {
            return $this->extensions[$method](...$args);
        }

        // Return control back to the connection
        return $this->connection->$method(...$args);
    }

    public function __toString()
    {
        return $this->toString();
    }
}
