# Database: Getting Started

Monarch provides a very simple wrapper around the [PDO database abstraction layer](https://www.php.net/manual/en/book.pdo.php). This wrapper is designed to be simple and easy to use, while still providing a good level of security and flexibility. Unlike many other database abstraction layers, Monarch does not attempt to hide the underlying database layer, but instead encourages the developer to learn the underlying technology and the SQL language.

## Configuring Database Connections

Database connections are configured in the `config/database.php` file. This file contains an array of database connections, each of which is an array of connection parameters. The `default` connection defines which connection is used by default, but you can specify a different connection by passing the connection name to the `db()` method.

```php
return [
    'default' => 'mysql',
    'mysql' => [
        'driver' => env('DB_DRIVER_DEFAULT', 'mysql'),
        'host' => env('DB_HOST_DEFAULT', 'localhost'),
        'port' => env('DB_PORT_DEFAULT', 3306),
        'user' => env('DB_USER_DEFAULT', 'root'),
        'password' => env('DB_PASSWORD_DEFAULT', 'root'),
        'database' => env('DB_DATABASE_DEFAULT', 'app'),
        'charset' => 'utf8mb4',
    ],
];
```

As you can see, the values should be set in the `.env` file. This allows you to easily switch between different database connections by changing the values in the `.env` file. This is also much more secure than including it in the `config/database.php` file itself, since the `.env` file is not saved as part of the repo.

## Connecting to the Database

Connections happen lazily as they are needed. So, no connection is made until a query is executed. This allows you to define multiple connections in the `config/database.php` file, but only connect to the database when you need to.

If you need to manually connect to the database, you can call the `connect()` method on the `Connection` object. This will establish a connection to the database using the connection parameters defined in the `config/database.php` file.

```php
db()->connect();
```

### Getting the connection type

You can get the connection type by calling the `driver()` method on the `Connection` object.

```php
$driver = db()->driver();
// returns: 'mysql'
```

## Using the Database

To use the database, you can call the `db()` function, which returns a `Monarch\Database\Connection` object. This object provides a simple wrapper around the PDO database abstraction layer, and allows you to execute queries and fetch results.

```php
$users = db()->query('SELECT * FROM users')->fetchAll();
```

This uses the default database connection, runs a query against that connection, and fetches all the results. You can also specify a different connection by passing the connection name as the first argument to the `db()` function.

```php
$users = db('mysql')->query('SELECT * FROM users')->fetchAll();
```

This will use the `mysql` connection instead of the default connection.

:: note
    The `db()` function will return a singleton instance of the `Connection` object for each unique configuration it uses. This means that you can call the `db()` function multiple times with the same configuration, and it will return the same `Connection` object each time. This allows you to easily share the same connection between different parts of your application.


## PDO Access

If you need to access the underlying PDO object directly, you can do so by grabbing the public `pdo` instance on the `Connection` object. This will return the `PDO` object that is used by the connection.

```php
$pdo = db()->pdo;
```

This can be useful if you need to access some of the more advanced features of the PDO object that are not exposed by the `Connection` object.

## Error Handling

If an error occurs during the execution of a query, an exception will be thrown. You can catch this exception and handle it as needed.

```php
try {
    $users = db()->run('SELECT * FROM users')
        ->fetchAll();
} catch (Exception $e) {
    // Handle the error
}
```

This will catch any exceptions that are thrown during the execution of the query, and allow you to handle them gracefully. You can then log the error, display an error message to the user, or take any other action that is appropriate.

## Table Methods

The database connection provides several methods for working with database tables.

### `tableExists(string $table): bool`

This method checks if a table exists in the database. It returns `true` if the table exists, and `false` if it does not.

```php
if (db()->tableExists('users')) {
    // The users table exists
}
```

### `tables(): array`

This method returns an array of all the tables in the database.

```php
$tables = db()->tables();

// returns:
[
    ['name' => 'users', 'view' => false],
    ['name' => 'posts', 'view' => false],
    ['name' => 'comments', 'view' => false],
]
```

### `columns(string $table): array`

This method returns an array of all the columns in a table.

```php
$columns = db()->columns('users');

// returns:
[
    [
        'field' => 'name',
        'type' => 'varchar(255)',
        'null' => true,
        'key' => null,
        'default' => null,
        'extra' => '',
    ],
]
```

### `columnNames(string $table): array`

This method returns an array of the column names in a table.

```php
$columns = db()->columnNames('users');

// returns:
['name', 'email', 'password']
```

### `primaryKey(string $table): ?string`

This method returns the primary key of a table, if one exists.

```php
$primaryKey = db()->primaryKey('users');

// returns:
'id'
```

### `createTable(string $table, array $columns): void`

This method creates a new table in the database. The first argument is the name of the table, and the second argument is an array of column definitions.

```php
db()->createTable('users', [
    'id INT AUTO_INCREMENT PRIMARY KEY',
    'name VARCHAR(255) NOT NULL',
    'email VARCHAR(255) NOT NULL',
]);
```

### `dropTable(string $table): void`

This method drops a table from the database.

```php
db()->dropTable('users');
```

### `indexes(string $table): array`

This method returns an array of all the indexes in a table.

```php
$indexes = db()->indexes('users');

// returns:
[
    ['name' => 'PRIMARY', 'unique' => true, 'primary' => true, 'columns' => ['id']],
    ['name' => 'email', 'unique' => false, 'primary' => false, 'columns' => ['email']],
]
```

### `indexExists(string $table, string $column): bool`

This method checks if an index exists on a column in a table.

```php
if (db()->indexExists('users', 'email')) {
    // The email index exists
}
```

### `foreignKeys(string $table): array`

This method returns an array of all the foreign keys in a table.

```php
$foreignKeys = db()->foreignKeys('users');

// returns:
[
    [
        'name' => 'users_role_id_foreign',
        'local' => 'role_id',
        'table' => 'roles',
        'foreign' => 'id',
    ],
]
```

### `foreignKeyExists(string $table, string $column): bool`

This method checks if a foreign key exists on a column in a table.

```php
if (db()->foreignKeyExists('users', 'role_id')) {
    // The role_id foreign key exists
}
```

### `disableForeignKeyConstraints(): void`

This method disables foreign key constraints for the current connection. This is often used in conjunction with `enableForeignKeys()` to temporarily disable foreign key constraints while making changes to the database.

```php
db()->disableForeignKeys();
```

### `enableForeignKeys(): void`

This method enables foreign key constraints for the current connection. This is often used in conjunction with `disableForeignKeys()` to temporarily disable foreign key constraints while making changes to the database.

```php
db()->enableForeignKeys();
```
