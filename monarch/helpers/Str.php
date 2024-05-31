<?php

namespace Monarch\Helpers;

use Random\RandomException;

class Str
{
    /**
     * Converts a string from snake_case to PascalCase.
     */
    public static function pascal(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', mb_strtolower($string))));
    }

    /**
     * Converts a string from snake_case to camelCase.
     */
    public static function camel(string $string): string
    {
        return str_replace(' ', '', lcfirst(ucwords(str_replace(['-', '_'], ' ', mb_strtolower($string)))));
    }

    /**
     * Converts a string to kebab-case.
     */
    public static function kebab(string $string): string
    {
        return str_replace([' ', '_'], '-', mb_strtolower($string));
    }

    /**
     * Converts a string to snake_case.
     */
    public static function snake(string $string): string
    {
        return str_replace(' ', '_', str_replace([' ', '-'], ' ', mb_strtolower($string)));
    }

    /**
     * Converts a string to Title Case.
     */
    public static function title(string $string): string
    {
        return ucwords(str_replace(['-', '_'], ' ', mb_strtolower($string)));
    }

    /**
     * Converts a string to slug-case.
     */
    public static function slug(string $string, string $divider = '-'): string
    {
        // replace non letter or digits by divider
        $string = preg_replace('~[^\pL\d]+~u', $divider, $string);

        // transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        // trim
        $string = trim($string, $divider);

        // remove duplicate divider
        $string = preg_replace('~-+~', $divider, $string);

        // lowercase
        $string = strtolower($string);

        if (empty($string)) {
            return 'n-a';
        }

        return $string;
    }

    /**
     * Check if a string contains another string.
     */
    public static function contains(string $haystack, string $needle): bool
    {
        return mb_strpos($haystack, $needle) !== false;
    }

    /**
     * Check if a string contains all of the given strings.
     */
    public static function containsAll(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (!self::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a string contains any of the given strings.
     */
    public static function length(string $string): int
    {
        return mb_strlen($string);
    }

    /**
     * Limit the number of characters in a string.
     */
    public static function limit(string $string, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }

        return rtrim(mb_substr($string, 0, $limit, 'UTF-8')) . $end;
    }

    /**
     * Generate a secure, random string.
     *
     * @throws RandomException
     */
    public static function random(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Limit the number of words in a string.
     */
    public static function words(string $string, int $wordLimit = 100, string $end = '...'): string
    {
        $words = explode(' ', $string);

        if (count($words) <= $words) {
            return $string;
        }

        return implode(' ', array_slice($words, 0, $wordLimit)) . $end;
    }
}
