<?php

namespace Monarch\Database;

class QueryBuilder
{
    private array $lines = [];
    private array $bindings = [];

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
     * Resets the query builder.
     */
    public function reset(): void
    {
        $this->lines = [];
        $this->bindings = [];
    }
}
