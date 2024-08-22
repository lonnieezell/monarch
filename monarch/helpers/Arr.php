<?php

declare(strict_types=1);

namespace Monarch\Helpers;

class Arr
{
    /**
    * Get an item from an array using "dot" notation.
    *
    * @param array $array
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (! is_array($array)) {
            return $default;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        // Search for the key in a dot-notated string,
        // allowwing for nested arrays and wildcards
        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
    * Determine if the given key exists in the provided array.
    *
    * @param array $array
    * @param string $key
    * @return bool
    */
    public static function has(array $array, string $key): bool
    {
        if (empty($array) || $key === '') {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a new array of all of the values that match the given $column, using
     * dot notation. If $keyAs is provided, the returned array will use the values
     * from that column as the keys.
     */
    public static function pluck(array $array, string $column, string $keyAs = null): array
    {
        $results = [];

        foreach ($array as $item) {
            $value = static::get($item, $column);

            if ($keyAs) {
                $results[static::get($item, $keyAs)] = $value;
            } else {
                $results[] = $value;
            }
        }

        return $results;
    }
}
