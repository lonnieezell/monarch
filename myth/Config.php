<?php namespace Myth;

/**
 * Class Config
 *
 * Simple way to retrieve config settings from local config files.
 *
 * @package Myth
 */
class Config extends StaticClass
{
    /**
     * Provides a local cache for config files
     * that we've already read.
     * @var array
     */
    protected $files = [];

    /**
     * Grab a config value from a file at app/config.
     *
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function get(string $key)
    {
        $keys = explode('.', $key);

        if (! count($keys)) {
            throw new \RuntimeException('Invalid config key: '. $key);
        }

        $file = array_shift($keys);

        if (! isset($this->files[$file])) {
            $path = APPPATH ."config/{$file}.php";

            if (! file_exists($path)) {
                throw new \RuntimeException('Config file not found: '. $path);
            }

            $this->files[$file] = include $path;
        }

        return dot_array_search($key, $this->files[$file]);
    }
}
