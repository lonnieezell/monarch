# Query Builder

While Monarch encourages using raw SQL queries, it also acknowledges that sometimes structuring your queries requires several steps. The query builder is a simple, lightweight tool that provides a handful of methods to help you build your queries easily.

## Query Builder Access

The query builder can be instantiated by simply creating a new instance of the `QueryBuilder` class.

```php
use Monarch\Database\QueryBuilder;

$query = new QueryBuilder();
```

Alternatively, it can be accessed fluently within the `Connection` class, through the `sql()` method. This method returns a new instance of the `QueryBuilder` class. The first argument is the SQL string and the second argument is an optional array of parameters. This is the recommended way to access the query builder, unless you have specific needs that require a new instance of the query builder.

```php
use Monarch\Database\Connection;

$query = db()->sql('SELECT * FROM users WHERE id = ?', [1])
    ->fetchAll();
```

Either way you choose to access the query builder, the methods available are the same.

## Query Builder Methods

The query builder provides a handful of methods to help you build your queries. These methods are chainable, so you can call them one after the other. The methods are as follows:

### `concat()`

The `concat()` method allows you to add another string to the SQL query. This might be used to add a `WHERE` clause, for example, or to add more after using the other methods, or simply to aid in readability.

```php
$query = db()
    ->sql('SELECT * FROM users WHERE id = ?', [1])
    ->concat(' AND name = ?', ['John'])
    ->fetchAll();
```

This can be chained as many times as needed. Each call to `concat()` will add the string to the end of the query, and the parameters will be added to the array of bound parameters.

### `when()`

The `when()` method allows you to conditionally add a string to the SQL query. The first argument must evaluate to a boolean, and the second argument is the string to add if the boolean is true. The third argument is an optional array of parameters to bind. The new SQL will only be added if the first argument evaluates to true.

```php
$query = db()
    ->sql('SELECT * FROM users')
    ->when($role, ' WHERE role = ?', [$role])
    ->concat(' AND active = ?', [1])
    ->fetchAll();
```

### `whenNot()`

The `whenNot()` method is the opposite of `when()`. It will only add the string to the SQL query if the first argument evaluates to false.

```php
$query = db()
    ->sql('SELECT * FROM users')
    ->whenNot($ignoreRole, ' WHERE role = ?', [$role])
    ->concat(' AND active = ?', [1])
    ->fetchAll();
```

### `each()`

The `each()` method allows you to loop through an array and add a string to the SQL query for each item in the array. The first argument is the array to loop through, and the second argument is a callback function that will be called for each item in the array. The callback function receives the Query Builder instance as the second argument so the other methods can be used to add additional SQL and bound values. It also passes the current $index as the third argument.

```php
$query = db()
    ->sql('SELECT * FROM users')
    ->each($roles, function($role, $query, $index) {
        $query->concat(' OR role = ?', [$role]);
    });
    ->fetchAll();
```

### `toSQL()`

The `toSQL()` method returns the SQL string that has been built so far. This can be useful for debugging purposes, or if you need to pass the SQL string to another method.

```php
$sql = db()
    ->sql('SELECT * FROM users')
    ->concat(' WHERE id = ?', [1])
    ->toSQL();
```

### `bindings()`

The `bindings()` method returns an array of the bound parameters that have been added so far. This can be useful for debugging purposes, or if you need to pass the bound parameters to another method.

```php
$query = $sql = db()
    ->sql('SELECT * FROM users')
    ->concat(' WHERE id = ?', [1]);
$sql = $query->toSQL();
$bindings = $query->bindings();
```

### `reset()`

The `reset()` method resets the query builder to its initial state. This can be useful if you need to reuse the query builder for another query, especially during testing.

```php
$query = db()
    ->sql('SELECT * FROM users')
    ->concat(' WHERE id = ?', [1]);
$query->reset();
```
