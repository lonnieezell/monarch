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

## Running Queries

The `Connection` class provides a single method for running queries: `run()`. This method takes a SQL query string as its first argument, and returns a `PDOStatement` object. You can then use this object to fetch the results of the query.

```php
$statement = db()->run('SELECT * FROM users');
$users = $statement->fetchAll();
```

This can be simplified by chaining the `run()` method with the `fetchAll()` method.

```php
$users = db()->run('SELECT * FROM users')
    ->fetchAll();
```

Many queries will require parameters to be passed in. You can do this by passing an array of parameters as the second argument to the `run()` method. These parameters will be automatically escaped and quoted, so you don't have to worry about SQL injection attacks.

```php
$user = db()->run('SELECT * FROM users WHERE id = ?', [1])
    ->fetch();

$users = db()->run('SELECT * FROM users WHERE role = ? AND status = ?', [
    'admin',
    'active'
])->fetchAll();
```

Behind the scenes, the `run()` method uses the [PDO::query()](https://www.php.net/manual/en/pdo.query.php) method to execute the query when no parameters are passed in. When parameters are passed in, it uses the [PDO::prepare()](https://www.php.net/manual/en/pdo.prepare.php) method to prepare the query, and then the [PDOStatement::execute()](https://www.php.net/manual/en/pdostatement.execute.php) method to execute the query with the parameters. This automatically handles escaping and quoting the parameters, so you don't have to worry about SQL injection attacks.

### Fetching Results

The `PDOStatement` object returned by the `run()` method provides a number of methods for fetching the results of the query. The most common methods are `fetch()`, `fetchAll()`, and `fetchColumn()`.

The [fetch()](https://www.php.net/manual/en/pdostatement.fetch.php) method fetches a single row from the result set, and returns it as an associative array.

```php
$user = db()->run('SELECT * FROM users WHERE id = ?', [1])
    ->fetch();
```
As well as when you are expecting a single row in the result, this can be used for queries that are expected to return many rows. When called multiple times, it will return the next row each time.

```php
$statement = db()->run('SELECT * FROM users');
while ($user = $statement->fetch()) {
    // Do something with the user
}
```

The [fetchAll()](https://www.php.net/manual/en/pdostatement.fetchall.php) method fetches all the rows from the result set, and returns them as an array of associative arrays. This loads all results into memory, so it should be used with caution when dealing with large result sets.

```php
$users = db()->run('SELECT * FROM users')
    ->fetchAll();
```

The [fetchColumn()](https://www.php.net/manual/en/pdostatement.fetchcolumn.php) method fetches a single column from the result set, and returns it as an array.

```php
$names = db()->run('SELECT name FROM users')
    ->fetchColumn();
```

You can also specify the column number to fetch, starting from 0.

```php
$names = db()->run('SELECT name, email FROM users')
    ->fetchColumn(1);
```

### Fetching Results as Objects

You can also fetch the results as objects by passing the class name to the `fetch()` and `fetchAll()` methods. This will return an instance of the specified class for each row in the result set.

```php
class User
{
    public $id;
    public $name;
    public $email;
}

$users = db()->run('SELECT * FROM users')
    ->fetchAll(PDO::FETCH_CLASS, User::class);
```

This will return an array of `User` objects, with the properties set to the values from the result set. You can then access the properties of the object as you would with any other object.

```php
foreach ($users as $user) {
    echo $user->name;
}
```

### Fetching Results as Key-Value Pairs

You can also fetch the results as key-value pairs by passing the column name to the `fetch()` and `fetchAll()` methods. This will return an associative array with the specified column as the key, and the specified column's value as the value.

```php
$users = db()->run('SELECT id, name FROM users')
    ->fetchAll(PDO::FETCH_KEY_PAIR);
```

This will return an array where the keys are the `id` column, and the values are the `name` column. You can then access the values by their keys. This only works when fetching exactly two columns.

```php
echo $users[1];
// [1 => 'John Doe']
```

### Fetching Results as Grouped Arrays

You can also fetch the results as grouped arrays by passing the column name to the `fetch()` and `fetchAll()` methods. This will return an associative array with the specified column as the key, and an array of rows with the specified column's value as the value.

```php
$users = db()->run('SELECT role, name, email FROM users')
    ->fetchAll(PDO::FETCH_GROUP);
```

This will return an array where the keys are the `role` column, and the values are arrays of rows with the same `role` value. You can then access the values by their keys.

```php
[
    'admin' => [
        ['name' => 'John Doe', 'email' => 'johndoe@example.com'],
        ['name' => 'Jane Doe', 'email' => 'janedoe@example.com'],
    ],
    'user' => [
        ['name' => 'Alice Smith', 'email' => 'alicesmith@example.com'],
        ['name' => 'Bob Jones', 'email' => 'bobsmith@example.com'],
    ],
]
```

### Inserting Data

Inserting data into the database is done using the `run()` method with an `INSERT` query. You can pass in an associative array of column names and values to insert the data.

```php
db()->run('INSERT INTO users (name, email) VALUES (:name, :email)', [
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
]);
```

This will insert a new row into the `users` table with the specified values. The keys in the associative array are used as the column names, and the values are used as the column values. The values are automatically escaped and quoted, so you don't have to worry about SQL injection attacks.

This example uses named placeholders, which are more readable and easier to maintain than positional placeholders, but can occasionally make things a little more challenging. Named placeholders are always prefixed with a single colon. You can also use positional placeholders if you prefer.

```php
db()->run('INSERT INTO users (name, email) VALUES (?, ?)', [
    'John Doe',
    'johndoe@example.com',
]);
```

### Updating Data

Updating data in the database is done using the `run()` method with an `UPDATE` query. You can pass in an associative array of column names and values to update the data.

```php
db()->run('UPDATE users SET name = :name, email = :email WHERE id = :id', [
    'name' => 'Jane Doe',
    'email' => 'janedoe@example.com',
    'id' => 1,
]);
```

This will update the row in the `users` table with the specified values, where the `id` column matches the specified value. The keys in the associative array are used as the column names, and the values are used as the column values. The values are automatically escaped and quoted, so you don't have to worry about SQL injection attacks.

### Deleting Data

Deleting data from the database is done using the `run()` method with a `DELETE` query. You can pass in a single
value to delete a row by its primary key.

```php
db()->run('DELETE FROM users WHERE id = ?', [1]);
```

This will delete the row in the `users` table where the `id` column matches the specified value. The value is automatically escaped and quoted.


## Transactions

You can use transactions to group multiple queries together into a single unit of work. This allows you to ensure that all the queries are executed successfully, or none of them are executed at all. This is useful when you need to make sure that a series of queries are executed atomically.

To start a transaction, you can call the `beginTransaction()` method on the `Connection->pdo` object. This will start a new transaction on the current connection. You can then run your queries as normal, and they will be executed within the transaction. If any of the queries fail, you can call the `rollBack()` method to roll back the transaction, and undo all the changes. If all the queries succeed, you can call the `commit()` method to commit the transaction, and save all the changes.

```php
$db = db();
$db->pdo->beginTransaction();

try {
    $db->run('INSERT INTO users (name, email) VALUES (:name, :email)', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com'
    ]);

    $db->run('INSERT INTO users (name, email) VALUES (:name, :email)', [
        'name' => 'Jane Doe',
        'email' => 'janedoe@example.com'
    ]);

    $db->pdo->commit();
} catch (Exception $e) {
    $db->pdo->rollBack();
    throw $e;
}
```

## PDO Access

If you need to access the underlying PDO object directly, you can do so by grabbing the public `pdo` instance on the `Connection` object. This will return the `PDO` object that is used by the connection.

```php
$pdo = db()->pdo;
```

This can be useful if you need to access some of the more advanced features of the PDO object that are not exposed by the `Connection` object.

## Error Handling

Monarch provides a simple error handling mechanism for database queries. If an error occurs during the execution of a query, an exception will be thrown. You can catch this exception and handle it as needed.

```php
try {
    $users = db()->run('SELECT * FROM users')
        ->fetchAll();
} catch (Exception $e) {
    // Handle the error
}
```

This will catch any exceptions that are thrown during the execution of the query, and allow you to handle them gracefully. You can then log the error, display an error message to the user, or take any other action that is appropriate.
