<?php

namespace Monarch\Database;

use DateTimeInterface;
use PDO;

interface DriverInterface
{
    /**
     * Connect to the database.
     *
     * Example:
     * db()->connect(['database' => ':memory:']);
     *
     * @throws PDOException
     */
    public function connect(?array $options = null): PDO;

    /**
     * Returns the correct date/time format for the database.
     */
    public function formatDateTime(DateTimeInterface $date): string;

    /**
     * Checks if a table exists in the database.
     *
     * Example:
     * db()->tableExists('users');
     *
     * @throws PDOException
     */
    public function tableExists(string $table): bool;

    /**
     * Get a list of all tables in the database.
     *
     * Example:
     * db()->tables();
     *
     * returns: [
     *    ['name' => 'users', 'view' => false],
     * ];
     *
     * @throws PDOException
     */
    public function tables(): array;

    /**
     * Get a list of all columns in a table.
     *
     * Example:
     * db()->columns('users');
     *
     * @throws PDOException
     */
    public function columns(string $table): array;

    /**
     * Get the column names for a table.
     *
     * Example:
     * db()->columnNames('users');
     *
     * @throws PDOException
     */
    public function columnNames(string $table): array;

    /**
     * Get the primary key for a table.
     *
     * Example:
     * db()->primaryKey('users');
     *
     * @throws PDOException
     */
    public function primaryKey(string $table): ?string;

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
    public function createTable(string $table, array $columns): void;

    /**
     * Drop a table from the database.
     *
     * Example:
     * db()->dropTable('users');
     *
     * @throws PDOException
     */
    public function dropTable(string $table): void;

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
    public function indexes(string $table): array;

    /**
     * Check if an index exists on a table.
     *
     * Example:
     * db()->indexExists('users', 'email');
     *
     * @throws PDOException
     */
    public function indexExists(string $table, string $column): bool;

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
    public function foreignKeys(string $table): array;

    /**
     * Check if a foreign key exists on a table.
     *
     * Example:
     * db()->foreignKeyExists('users', 'role_id');
     *
     * @throws PDOException
     */
    public function foreignKeyExists(string $table, string $column): bool;

    /**
     * Disables foreign key constraints.
     *
     * Example:
     * db()->disableForeignKeys();
     *
     * @throws PDOException
     */
    public function disableForeignKeys(): void;

    /**
     * Enables foreign key constraints.
     *
     * Example:
     * db()->enableForeignKeys();
     *
     * @throws PDOException
     */
    public function enableForeignKeys(): void;
}
