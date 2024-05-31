<?php

namespace Monarch\Helpers;

use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;

class Files
{
    /**
     * Returns a generator that yields all PHP files in a directory.
     *
     * @throws RuntimeException
     */
    public static function in(string $path, string $pattern='\.php$'): Generator
    {
        if (! is_dir($path)) {
            throw new RuntimeException("{$path} is not a directory ");
        }

        $iterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($iterator);
        $iterator = new RegexIterator($iterator, "/{$pattern}/", RegexIterator::MATCH);

        yield from $iterator;
    }

    /**
     * Read the contents of a file.
     *
     * @throws RuntimeException
     */
    public static function read(string $path): string
    {
        // Ensure the file exists
        if (! file_exists($path)) {
            throw new RuntimeException("File does not exist at path: {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * Write a string to a file.
     *
     * @throws RuntimeException
     */
    public static function write(string $path, string $contents): void
    {
        // Ensure the directory exists
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $contents);
    }

    /**
     * Append a string to a file.
     *
     * @throws RuntimeException
     */
    public static function append(string $path, string $contents): void
    {
        $result = file_put_contents($path, $contents, FILE_APPEND);

        if (! $result) {
            throw new RuntimeException("Failed to append to file at path: {$path}");
        }
    }

    /**
     * Copy a file from one location to another. If the destination directory
     * does not exist, it will be created.
     *
     * @throws RuntimeException
     */
    public static function copy(string $source, string $destination): void
    {
        if (! file_exists($source)) {
            throw new RuntimeException("File does not exist at path: {$source}");
        }

        // Ensure the destination directory exists
        $dir = dirname($destination);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($source, $destination);
    }

    /**
     * Move a file from one location to another.
     * If the destination directory does not exist, it will be created.
     *
     * @throws RuntimeException
     */
    public static function move(string $source, string $destination): void
    {
        self::copy($source, $destination);

        // Verify it was copied. If so, delete the original
        if (! file_exists($destination)) {
            throw new RuntimeException("Failed to move file {basename($source)}");
        }

        self::delete($source);
    }

    /**
     * Get the size of a file.
     *
     * @throws RuntimeException
     */
    public static function size(string $path): int
    {
        if (is_dir($path)) {
            throw new RuntimeException("Cannot get size of a directory: {$path}");
        }

        if (! file_exists($path)) {
            throw new RuntimeException("File does not exist: {$path}");
        }

        $result = filesize($path);

        if ($result === false) {
            throw new RuntimeException("Failed to get file size: {$path}");
        }

        return $result;
    }

    /**
     * Read the contents of a JSON file into an array.
     *
     * @throws RuntimeException
     */
    public static function readJson(string $path): array
    {
        // Ensure the file exists
        if (! file_exists($path)) {
            throw new RuntimeException("File does not exist at path: {$path}");
        }

        $contents = file_get_contents($path);

        return json_decode($contents, true);
    }

    /**
     * Write an array to a JSON file.
     *
     * Returns the final path that was written to.
     *
     * @throws RuntimeException
     */
    public static function writeJson(string $path, array $data): string
    {
        // Ensure the directory exists
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Ensure we have a JSON file extension
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'json') {
            $path .= '.json';
        }

        // Write the file
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($path, $json);

        return $path;
    }

    /**
     * Append an array to a JSON file.
     *
     * @throws RuntimeException
     */
    public static function appendJson(string $path, array $data): void
    {
        $currentData = self::readJson($path);
        $newData = array_merge($currentData, $data);
        self::writeJson($path, $newData);
    }

    /**
     * Delete a key from a JSON file.
     *
     * @throws RuntimeException
     */
    public static function deleteJson(string $path, string $key): void
    {
        $data = self::readJson($path);

        if (! isset($data[$key])) {
            throw new RuntimeException("Key does not exist in JSON file: {$key}");
        }

        unset($data[$key]);

        self::writeJson($path, $data);
    }

    /**
     * Delete a file.
     *
     * @throws RuntimeException
     */
    public static function delete(string $path): void
    {
        if (! file_exists($path)) {
            throw new RuntimeException("File does not exist at path: {$path}");
        }

        unlink($path);

        // Double-check that the file was deleted
        if (file_exists($path)) {
            throw new RuntimeException("Failed to delete file at path: {$path}");
        }
    }
}
