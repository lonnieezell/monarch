# Str Helper

The `Str` helper class provides a set of static methods to manipulate strings.

## Methods

### `pascal(string $value): string`

Convert the given string to PascalCase.

```php
use Monarch\Helpers\Str;

Str::pascal('foo_bar');

// FooBar
```

### `camel(string $value): string`

Convert the given string to camelCase.

```php
use Monarch\Helpers\Str;

Str::camel('foo_bar');

// fooBar
```

### `kebab(string $value): string`

Convert the given string to kebab-case.

```php
use Monarch\Helpers\Str;

Str::kebab('fooBar');

// foo-bar
```

### `snake(string $value): string`

Convert the given string to snake_case.

```php
use Monarch\Helpers\Str;

Str::snake('FooBar');

// foo_bar
```

### `slug(string $value): string`

Convert the given string to a URL friendly slug.

```php
use Monarch\Helpers\Str;

Str::slug('Foo Bar');

// foo-bar
```

### `title(string $value): string`

Convert the given string to Title Case.

```php
use Monarch\Helpers\Str;

Str::title('foo_bar');

// Foo Bar
```

### `contains(string $haystack, string $needle): bool`

Check if the given string contains the given substring.

```php
use Monarch\Helpers\Str;

Str::contains('foo_bar', 'bar');

// true
```

### `containsAll(string $haystack, array $needles): bool`

Check if the given string contains all the given substrings.

```php
use Monarch\Helpers\Str;

Str::containsAll('foo_bar', ['foo', 'bar']);

// true
```

### `length(string $value): int`

Get the length of the given string.

```php
use Monarch\Helpers\Str;

Str::length('foo_bar');

// 7
```

### `limit(string $value, int $limit, string $end = '...'): string`

Limit the number of characters in the given string.

```php
use Monarch\Helpers\Str;

Str::limit('foo_bar', 3);

// foo...
```

A custom ending can be provided as the third argument.

```php
use Monarch\Helpers\Str;

Str::limit('foo_bar', 3, '!!!');

// foo!!!
```

### `words(string $value, int $words, string $end = '...'): string`

Limit the number of words in the given string.

```php
use Monarch\Helpers\Str;

Str::words('foo bar baz', 2);

// foo bar...
```

A custom ending can be provided as the third argument.

```php
use Monarch\Helpers\Str;

Str::words('foo bar baz', 2, '!!!');

// foo bar!!!
```

### `random(int $length = 16): string`

Generate a random string of the given length in a secure manner.

```php
use Monarch\Helpers\Str;

Str::random(8);

// 5e7b3a9c
```
