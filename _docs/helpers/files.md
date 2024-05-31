# Files

## File Helper

The Files helper class contains functions that assist in working with files.

### `in($path)`

The `in` method returns a boolean value indicating whether the given file exists in the specified path.

```php
use Monarch\Helpers\Files;

if (Files::in('path/to/file.txt')) {
    echo 'File exists!';
} else {
    echo 'File does not exist!';
}
```

### `read($path)`

The `read` method reads the contents of the specified file and returns it as a string.

```php
use Monarch\Helpers\Files;

$content = Files::read('path/to/file.txt');

echo $content;
```

### `write($path, $content)`

The `write` method writes the specified content to the specified file. If the containing directory does not exist, it will attempt to create it.

```php
use Monarch\Helpers\Files;

Files::write('path/to/file.txt', 'Hello, world!');
```

### `append($path, $content)`

The `append` method appends the specified content to the specified file.

```php
use Monarch\Helpers\Files;

Files::append('path/to/file.txt', 'Hello, world!');
```

### `delete($path)`

The `delete` method deletes the specified file.

```php
use Monarch\Helpers\Files;

Files::delete('path/to/file.txt');
```

### `copy($source, $destination)`

The `copy` method copies the specified file to the specified destination. If the containing directory does not exist, it will attempt to create it. If the destination file already exists, it will be overwritten.

```php
use Monarch\Helpers\Files;

Files::copy('path/to/file.txt', 'path/to/destination/file.txt');
```

### `move($source, $destination)`

The `move` method moves the specified file to the specified destination. If the containing directory does not exist, it will attempt to create it. If the destination file already exists, it will be overwritten.

```php
use Monarch\Helpers\Files;

Files::move('path/to/file.txt', 'path/to/destination/file.txt');
```

### `size($path)`

The `size` method returns the size of the specified file in bytes.

```php
use Monarch\Helpers\Files;

$size = Files::size('path/to/file.txt');

echo $size;
```

### `readJson($path)`

The `readJson` method reads the contents of the specified JSON file and returns it as an array.

```php
use Monarch\Helpers\Files;

$data = Files::readJson('path/to/file.json');

print_r($data);
```

### `writeJson($path, $data)`

The `writeJson` method writes the specified data to the specified JSON file. If the containing directory does not exist, it will attempt to create it.

```php
use Monarch\Helpers\Files;

$data = ['name' => 'John Doe', 'age' => 30];

Files::writeJson('path/to/file.json', $data);
```

### `appendJson($path, $data)`

The `appendJson` method appends the specified data to the specified JSON file. It uses `array_merge` to merge the existing data with the new data.

```php
use Monarch\Helpers\Files;

$data = ['name' => 'Jane Doe', 'age' => 25];

Files::appendJson('path/to/file.json', $data);
```

### `deleteJson($path, $key)`

The `deleteJson` method deletes the specified key from the specified JSON file.

```php
use Monarch\Helpers\Files;

Files::deleteJson('path/to/file.json', 'name');
```
