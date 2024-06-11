# Extending the Database

The database package is designed to be easily extended with new methods and functionality. The [QueryBuilder](./query_builder.md) is a good example of this. It is a simple, lightweight tool that provides a handful of methods to help you build your queries easily. This section will show you how to extend the database package with your own methods.

Extending the database package can be done to provide convenience methods for your application, or to add functionality that is not provided by the core package. For example, you might want to add methods to work with views or stored procedures, or to provide a more convenient way to work with certain types of queries. Or you might find yourself repeating the same code in multiple places and want to encapsulate that code in a method.

Extending the database layer happens in two areas: core extensions and query builder extensions.

## Core Extensions

Core extensions are methods that are added directly to the `Connection` class. These methods are available on all instances of the `Connection` class, and can be accessed fluently.

To add a core extension, create a new class that extends `Monarch\Database\ExtensionInterface`. This interface requires you to implement a single method, `extend()`. This method takes an instance of the `Connection` class as its only argument. You can then add your methods to the `Connection` class using the `register()` method on the connection instance.

Here is an example of how the QueryBuilder registers the `sql()` method:

```php
use Monarch\Database\Connection;
use Monarch\Database\ExtensionInterface;

class QueryBuilder implements ExtensionInterface
{
    public static function extend(Connection $connection): void
    {
        $connection->register('sql', fn($sql, $bindings = null) => new QueryBuilder::instance()->sql($sql, $bindings));
    }
}
```

This registers the `sql()` method on the `Connection` class, which returns a new instance of the `QueryBuilder` class. This method can then be accessed fluently on the `Connection` class. You can add as many methods as you like in this way.

!!! tip

    It is best practice to provide a single entry point for your extensions, such as the `sql()` method, to minimize the chances for collisions with other extensions.

## Query Builder Extensions

Query builder extensions are methods that are added to the `QueryBuilder` class. These methods are available on all instances of the `QueryBuilder` class, and can be accessed fluently.

// Finish me....

## Configuration

To let Monarch know about your core extension, you need to add it to the `extensions` array in the `config/database.php` file. This array should contain the fully qualified class names of your extensions.

```php
return [
    //
    'extensions' => [
        QueryBuilder::class,
    ],
];
```

QueryBuilder extensions should be added to the `queryBuilderExtensions` array in the `config/database.php` file. This array should contain the fully qualified class names of your extensions.

```php
return [
    //
    'queryBuilderExtensions' => [
        QueryBuilderExtension::class,
    ],
];
```
