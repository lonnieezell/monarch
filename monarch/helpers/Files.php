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
    public static function in(string $path): Generator
    {
        if (! is_dir($path)) {
            throw new RuntimeException("{$path} is not a directory ");
        }

        $iterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($iterator);
        $iterator = new RegexIterator($iterator, '/\.php$/', RegexIterator::MATCH);

        yield from $iterator;
    }
}
