<?php

declare(strict_types=1);

namespace Monarch;

use RuntimeException;

/**
 * Class DotEnv
 *
 * Loads environment variables in from a .env file
 * located in ROOTPATH.
 *
 * Code from https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i
 *
 * @package Myth
 */
class DotEnv
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;


    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Loads the .env file and parses it
     */
    public function load(): void
    {
        if (!is_file($this->path)) {
            return;
        }

        if (!is_readable($this->path)) {
            throw new RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
