<?php

namespace Monarch\Database;

use PDO;

interface DatabaseInterface
{
    /**
     * Connect to the database.
     */
    public function connect(?array $options = null): PDO;

    /**
     * Checks if a table exists in the database.
     *
     * @return bool
     */
    public function tableExists(string $table): bool;

    /**
     * Get a list of all tables in the database.
     */
    public function tables(): array;

    /**
     * Get a list of all columns in a table.
     */
    public function columns(string $table): array;

    /**
     * Get the column names for a table.
     */
    public function columnNames(string $table): array;

    /**
     * Get the primary key for a table.
     */
    public function primaryKey(string $table): ?string;

    /**
     * Create a new table in the database.
     */
    public function createTable(string $table, array $columns): void;

    /**
     * Drop a table from the database.
     */
    public function dropTable(string $table): void;

    /**
     * Add a column to a table.
     */
    public function addColumn(string $table, string $column, string $type): void;

    /**
     * Drop a column from a table.
     */
    public function dropColumn(string $table, string $column): void;

    /**
     * Add an index to a table.
     */
    public function addIndex(string $table, string $column): void;

    /**
     * Drop an index from a table.
     */
    public function dropIndex(string $table, string $column): void;

    /**
     * Check if an index exists on a table.
     */
    public function indexExists(string $table, string $column): bool;

    /**
     * Add a foreign key to a table.
     */
    public function addForeignKey(string $table, string $column, string $foreignTable, string $foreignColumn): void;

    /**
     * Drop a foreign key from a table.
     */
    public function dropForeignKey(string $table, string $column): void;

    /**
     * Check if a foreign key exists on a table.
     */
    public function foreignKeyExists(string $table, string $column): bool;

    /**
     * Disable foreign key checks.
     */
    public function disableForeignKeys(): void;

    /**
     * Enable foreign key checks.
     */
    public function enableForeignKeys(): void;
}
