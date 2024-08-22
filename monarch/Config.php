<?php

declare(strict_types=1);

namespace Monarch;

use Monarch\Helpers\Arr;
use RuntimeException;

/**
 * Class Config
 *
 * Simple way to retrieve config settings from local config files.
 *
 * @package Myth
 */
class Config
{
    /**
     * Provides a local cache for config files
     * that we've already read.
     * @var array
     */
    protected $files = [];

    public static $instance;

    public static function factory()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Grab a config value from a file at app/config.
     *
     *
     * @return array|mixed|null
     */
    public function get(string $key)
    {
        $keys = explode('.', $key);

        if ($keys === []) {
            throw new RuntimeException('Invalid config key: '. $key);
        }

        $file = array_shift($keys);

        if (! isset($this->files[$file])) {
            $path = ROOTPATH ."config/{$file}.php";

            if (! file_exists($path)) {
                throw new RuntimeException('Config file not found: '. $path);
            }

            $this->files[$file] = include $path;
        }

        if (count($keys) === 0) {
            return $this->files[$file] ?? null;
        }

        return Arr::get($this->files[$file], implode('.', $keys));
    }

    /**
     * Allows for mocking of config files during testing.
     */
    public function mock(string $file, array $data)
    {
        // If the first key is numeric, we're replacing the entire file.
        if (is_numeric(array_key_first($data))) {
            $this->files[$file] = $data;
            return;
        }

        // Otherwise we're just replacing the given key(s)
        // in the original config file.
        if (! isset($this->files[$file])) {
            $path = ROOTPATH ."config/{$file}.php";

            if (! file_exists($path)) {
                throw new RuntimeException('Config file not found: '. $path);
            }

            $this->files[$file] = include $path;
        }

        $this->files[$file] = array_merge($this->files[$file], $data);
    }
}
