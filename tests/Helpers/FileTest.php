<?php

use Monarch\Helpers\Files;

describe('Files', function () {
    test('in', function () {
        $path = WRITEPATH;

        // Create 2 temporary files, one PHP one TEXT
        file_put_contents($path .'test1.php', '<?php echo "Test 1";');
        file_put_contents($path .'test2.txt', 'Test 2');

        // Assert that the generator yields all PHP files in the specified directory
        foreach (Files::in($path) as $file) {
            expect($file->getExtension())->toEqual('php');
        }

        // Clean up the temporary files
        unlink($path .'test1.php');
        unlink($path .'test2.txt');
    });

    test('read file', function () {
        $path = WRITEPATH .'test.txt';
        $expectedContent = 'Test content';

        // Create a temporary file with test content
        file_put_contents($path, $expectedContent);

        // Read the file
        $content = Files::read($path);

        // Assert that the content matches the expected content
        expect($content)->toEqual($expectedContent);

        // Clean up the temporary file
        unlink($path);
    });

    test('read file throws exception if doesnt exist', function () {
        $path = '/path/to/non-existent-file.txt';

        // Assert that an exception is thrown when attempting to read a non-existent file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File does not exist at path: {$path}");

        Files::read($path);
    });

    test('write file', function () {
        $path = WRITEPATH .'test/test.txt';
        $content = 'Test content';

        // Write the content to a file
        $writtenPath = Files::write($path, $content);

        // Assert that the file was created and contains the correct content
        expect($path)->toBeFile();
        expect(file_get_contents($path))->toEqual($content);

        // Clean up the created file
        unlink($path);
        rmdir(dirname($path));
    });

    test('write file creates directory', function () {
        $path = WRITEPATH .'test1/test2/test.txt';
        $content = 'Test content';

        // Write the content to a file
        Files::write($path, $content);

        // Assert that the file was created and contains the correct content
        expect($path)->toBeFile();
        expect(file_get_contents($path))->toEqual($content);

        // Clean up the created file
        unlink($path);
        rmdir(dirname($path));
    });

    test('append', function () {
        $path = WRITEPATH .'test.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($path, $content);

        // Append additional content to the file
        Files::append($path, 'Additional content');

        // Assert that the file contains the original and additional content
        expect(file_get_contents($path))->toEqual($content .'Additional content');

        // Clean up the temporary file
        unlink($path);
    });

    test('copy', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test-copy.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);

        // Copy the file to a new location
        Files::copy($source, $destination);

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created files
        unlink($source);
        unlink($destination);
    });

    test('copy throws exception if source does not exist', function () {
        $source = '/path/to/non-existent-file.txt';
        $destination = WRITEPATH .'test-copy.txt';

        // Assert that an exception is thrown when attempting to copy a non-existent file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File does not exist at path: {$source}");

        Files::copy($source, $destination);
    });

    test('copy creates directory', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test/test-copy.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);

        // Copy the file to a new location
        Files::copy($source, $destination);

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created files
        unlink($source);
        unlink($destination);
        rmdir(dirname($destination));
    });

    test('copy overwrites existing file', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test-copy.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);
        file_put_contents($destination, 'Existing content');

        // Copy the file to a new location
        Files::copy($source, $destination);

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created files
        unlink($source);
        unlink($destination);
    });

    test('move', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test-move.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);

        // Move the file to a new location
        Files::move($source, $destination);

        // Assert that the source file no longer exists
        expect($source)->not->toBeFile();

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created file
        unlink($destination);
    });

    test('move throws exception if source does not exist', function () {
        $source = '/path/to/non-existent-file.txt';
        $destination = WRITEPATH .'test-move.txt';

        // Assert that an exception is thrown when attempting to move a non-existent file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File does not exist at path: {$source}");

        Files::move($source, $destination);
    });

    test('move creates directory', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test/test-move.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);

        // Move the file to a new location
        Files::move($source, $destination);

        // Assert that the source file no longer exists
        expect($source)->not->toBeFile();

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created file
        unlink($destination);
        rmdir(dirname($destination));
    });

    test('move overwrites existing file', function () {
        $source = WRITEPATH .'test.txt';
        $destination = WRITEPATH .'test-move.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($source, $content);
        file_put_contents($destination, 'Existing content');

        // Move the file to a new location
        Files::move($source, $destination);

        // Assert that the source file no longer exists
        expect($source)->not->toBeFile();

        // Assert that the destination file was created and contains the correct content
        expect($destination)->toBeFile();
        expect(file_get_contents($destination))->toEqual($content);

        // Clean up the created file
        unlink($destination);
    });

    test('size', function () {
        $path = WRITEPATH .'test.txt';
        $content = 'Test content';

        // Create a temporary file with test content
        file_put_contents($path, $content);

        // Get the size of the file
        $size = Files::size($path);

        // Assert that the size matches the expected size
        expect($size)->toEqual(filesize($path));

        // Clean up the temporary file
        unlink($path);
    });

    test('size throws exception if file does not exist', function () {
        $path = '/path/to/non-existent-file.txt';

        // Assert that an exception is thrown when attempting to get the size of a non-existent file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File does not exist: {$path}");

        Files::size($path);
    });

    test('size throws exception if file is a directory', function () {
        $path = WRITEPATH .'test';

        // Create a temporary directory
        mkdir($path);

        // Assert that an exception is thrown when attempting to get the size of a directory
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot get size of a directory: {$path}");

        Files::size($path);

        // Clean up the temporary directory
        rmdir($path);
    });

    test('read json file', function () {
        $path = WRITEPATH .'test.json';
        $expectedData = ['name' => 'John Doe', 'age' => 30];

        // Create a temporary JSON file with test data
        file_put_contents($path, json_encode($expectedData));

        // Read the JSON file
        $data = Files::readJson($path);

        // Assert that the data matches the expected data
        expect($data)->toEqual($expectedData);

        // Clean up the temporary file
        unlink($path);
    });

    test('write json file', function () {
        $path = WRITEPATH .'test.json';
        $data = ['name' => 'John Doe', 'age' => 30];

        // Write the data to a JSON file
        $writtenPath = Files::writeJson($path, $data);

        // Assert that the returned path matches the expected path
        expect($writtenPath)->toEqual($path);

        // Assert that the JSON file was created and contains the correct data
        expect($path)->toBeFile();
        expect(json_decode(file_get_contents($path), true))->toEqual($data);

        // Clean up the created file
        unlink($path);
    });

    test('write json file creates directory', function () {
        $path = WRITEPATH .'test/test.json';
        $data = ['name' => 'John Doe', 'age' => 30];

        // Write the data to a JSON file
        $writtenPath = Files::writeJson($path, $data);

        // Assert that the returned path matches the expected path
        expect($writtenPath)->toEqual($path);

        // Assert that the JSON file was created and contains the correct data
        expect($path)->toBeFile();
        expect(json_decode(file_get_contents($path), true))->toEqual($data);

        // Clean up the created file
        unlink($path);
        rmdir(dirname($path));
    });

    test('write json file creates nested directories', function () {
        $path = WRITEPATH .'test1/test2/test.json';
        $data = ['name' => 'John Doe', 'age' => 30];

        // Write the data to a JSON file
        $writtenPath = Files::writeJson($path, $data);

        // Assert that the returned path matches the expected path
        expect($writtenPath)->toEqual($path);

        // Assert that the JSON file was created and contains the correct data
        expect($path)->toBeFile();
        expect(json_decode(file_get_contents($path), true))->toEqual($data);

        // Clean up the created file
        unlink($path);
        rmdir(dirname($path));
    });

    test('write json file adds json extension', function () {
        $path = WRITEPATH .'test';
        $data = ['name' => 'John Doe', 'age' => 30];

        // Write the data to a JSON file
        $writtenPath = Files::writeJson($path, $data);

        // Assert that the returned path matches the expected path
        expect($writtenPath)->toEqual($path .'.json');

        // Assert that the JSON file was created and contains the correct data
        expect($path .'.json')->toBeFile();
        expect(json_decode(file_get_contents($path .'.json'), true))->toEqual($data);

        // Clean up the created file
        unlink($path .'.json');
    });

    test('delete file', function () {
        $path = WRITEPATH .'test.json';

        // Create a temporary file
        file_put_contents($path, 'Test content');

        // Delete the file
        Files::delete($path);

        // Assert that the file no longer exists
        expect($path)->not->toBeFile();
    });

    test('delete file throws exception if file does not exist', function () {
        $path = '/path/to/non-existent-file.txt';

        // Assert that an exception is thrown when attempting to delete a non-existent file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File does not exist at path: {$path}");

        Files::delete($path);
    });
});
