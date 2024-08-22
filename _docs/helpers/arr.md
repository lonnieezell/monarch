# Array Helper

The `Arr` helper class provides a set of static methods for working with Arrays.

## Methods

- [Array Helper](#array-helper)
  - [Methods](#methods)
  - [get](#get)
  - [has](#has)
  - ['pluck'](#pluck)

## get

The `get` method retrieves a value from an array using dot notation.

```php
use Monarch\Helpers\Arr;

$array = ['products' => ['guitar' => ['price' => 100]]];
$price = Arr::get($array, 'products.guitar.price');
// 100
```

## has

The `has` method checks if a given key exists in an array using dot notation.

```php
use Monarch\Helpers\Arr;

$array = ['products' => ['guitar' => ['price' => 100]]];

$hasGuitar = Arr::has($array, 'products.guitar');
// true

$hasPrice = Arr::has($array, 'products.guitar.price');
// true
```

## 'pluck'

The `pluck` method retrieves all of the values for a given key from an array using dot notation.

```php
use Monarch\Helpers\Arr;

$array = [
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
];

$plucked = Arr::pluck($array, 'name');
// ['Desk', 'Chair']

$array = [
    ['user' => ['name' => 'John', 'age' => 26]],
    ['user' => ['name' => 'Jane', 'age' => 28]],
];

$plucked = Arr::pluck($array, 'user.name');
// ['john', 'jane']
```

You can also specify a column to use as a custom key for the returned array:

```php
$array = [
    ['user' => ['id' => 10, 'name' => 'John', 'age' => 26]],
    ['user' => ['id' => 20, 'name' => 'Jane', 'age' => 28]],
];

$plucked = Arr::pluck($array, 'user.name', 'user.id');
// [10 => 'John', 20 => 'Jane']
```
