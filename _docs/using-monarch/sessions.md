# Sessions

Sessions are a way to store information (in the form of variables) to be used across multiple pages. This is useful for storing user information, such as their username, or for storing information that needs to be used across multiple pages, such as a shopping cart.

Monarch uses the PHP `$_SESSION` superglobal to store session data. This is a built-in feature of PHP and is used to store session data across multiple pages.

## Choosing a Session Handler

By default, Monarch uses the built-in PHP session handler. This is a file-based session handler that stores session data in files on the server. This is a simple and easy-to-use session handler that works well for most applications when run on a single server.

However, if you are running your application on multiple servers, or if you need to store a large amount of session data, you may want to consider using a different session handler. Monarch supports the following session handlers:

### File Session Handler

The file session handler stores session data in files on the server. This is the default session handler provided by PHP.

### SQLite Session Handler

The SQLite session handler stores session data in an SQLite database. This is a good choice if you need to store a large amount of session data, or if you are running your application on multiple servers, especially when using `libSqlite` or a distributed provider like [Turso](https://turso.tech/).

To use the SQLite session handler, you need to set the `sessionHandler` configuration option to `sqlite` in your `config/app.php` file. You also need to provide the location of the database file using the `sessionSavePath` configuration option. For example:

```php
return [
    'sessionHandler' => 'sqlite',
    'sessionSavePath' => '/path/to/sqlite.db',
];
```

This uses the native SQLite session handler provided by the SQLite extension.

### Redis Session Handler

The Redis session handler stores session data in a Redis database. This is a good choice if you need to store a large amount of session data, or if you are running your application on multiple servers.

To use the Redis session handler, you need to set the `sessionHandler` configuration option to `redis` in your `config/app.php` file. You also need to provide the connection details for your Redis server using the `sessionSavePath` configuration option. For example:

```php
return [
    'sessionHandler' => 'redis',
    'sessionSavePath' => 'tcp://10.133.14.9:6379?auth=yourverycomplexpasswordhere',
];
```

This uses the native session handler provided by the Redis extension.

### Memcached Session Handler

The [Memcached session handler ](https://www.php.net/manual/en/memcached.sessions.php) stores session data in a Memcached server. This is a good choice if you need to store a large amount of session data, or if you are running your application on multiple servers.

To use the Memcached session handler, you need to set the `sessionHandler` configuration option to `memcached` in your `config/app.php` file. You also need to provide the connection details for your Memcached server using the `sessionSavePath` configuration option. For example:

```php
return [
    'sessionHandler' => 'memcached',
    'sessionSavePath' => 'localhost:11211',
];
```

This uses the native session handler provided by the Memcached extension.
