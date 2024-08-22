<?php

declare(strict_types=1);

use Monarch\View\Meta;
use Monarch\Config;
use Monarch\Database\Connection;
use Monarch\HTTP\CSRF;
use Monarch\HTTP\Response;
use Monarch\Validation\Validation;
use Monarch\View\Escaper;
use Somnambulist\Components\Validation\Factory;

/**
 * Retrieves a key from config files.
 * The first word should be the name of the config file,
 * any additional words are treated as nested parts of the array.
 *
 * // Given this in the config file:
 * 'mail' => [
 *      'from' => ['name' => 'foo', 'email' => 'foo@example.com']
 * ]
 * // use this to retrieve the name
 * config('mail.from.name')
 *
 * @param string $key
 *
 * @return mixed|Config instance
 */
if (! function_exists('config')) {
    function config(?string $key=null): mixed
    {
        return $key === null
            ? Config::factory()
            : Config::factory()->get($key);
    }
}

/**
 * Generates a CSRF token
 *
 * You can lock the token to a the current URI path by passing
 * true as the first argument.
 */
if (! function_exists('csrfInput')) {
    function csrfInput(bool $lock = false): string
    {
        return CSRF::input($lock);
    }
}

/**
 * Generates a CSRF token
 *
 * You can lock the token to a the current URI path by passing
 * true as the first argument.
 */
if (! function_exists('csrf')) {
    function csrf(bool $lock = false): string
    {
        return CSRF::token($lock);
    }
}

/**
 * Returns a new Connection instance.
 * If no connection name is provided, the default connection
 * will be used.
 */
if (! function_exists('db')) {
    function db(?string $connectionName = null): Connection
    {
        if ($connectionName === null) {
            $connectionName = ENVIRONMENT === 'test'
                ? 'test'
                : config('database.default');
        }

        $config = config('database.' . $connectionName);

        return Connection::createWithConfig($config);
    }
}

/**
 * Searches an array through dot syntax. Supports
 * wildcard searches, like foo.*.bar
 *
 * Originally written by me for CodeIgniter 4 framework
 *
 * @param string $index
 * @param array  $array
 *
 * @return mixed|null
 */
if (! function_exists('dot_array_search')) {
    function dot_array_search(string $index, array $array)
    {
        $segments = explode('.', rtrim(rtrim($index, '* '), '.'));

        return _array_search_dot($segments, $array);
    }

    /**
     * Used by dot_array_search to recursively search the
     * array with wildcards.
     *
     * Originally written by me for CodeIgniter 4 framework
     *
     *
     * @return mixed|null
     */
    function _array_search_dot(array $indexes, array $array)
    {
        // Grab the current index
        $currentIndex = $indexes
        ? array_shift($indexes)
        : null;

        if ((empty($currentIndex) && (int) $currentIndex !== 0) || (! isset($array[$currentIndex]) && $currentIndex !== '*')) {
            return null;
        }

        // Handle Wildcard (*)
        if ($currentIndex === '*') {
            // If $array has more than 1 item, we have to loop over each.
            foreach ($array as $value) {
                $answer = _array_search_dot($indexes, $value);

                if ($answer !== null) {
                    return $answer;
                }
            }

            // Still here after searching all child nodes?
            return null;
        }

        // If this is the last index, make sure to return it now,
        // and not try to recurse through things.
        if ($indexes === []) {
            return $array[$currentIndex];
        }

        // Do we need to recursively search this value?
        if (is_array($array[$currentIndex]) && $array[$currentIndex]) {
            return _array_search_dot($indexes, $array[$currentIndex]);
        }

        // Otherwise we've found our match!
        return $array[$currentIndex];
    }
}

/**
 * Get a value from the environment, or return the default value.
 *
 * @param string      $key
 * @param string|null $default
 *
 * @return array|false|string|null
 */
if (! function_exists('env')) {
    function env(string $key, ?string $default = null)
    {
        $value = getenv($key);

        return $value !== false
        ? $value
        : $default;
    }
}

/**
 * Escapes a string for HTML output
 *
 * Used when outputting user-contributed content to the
 * HTML body of a page.
 *
 * Example:
 *  <?= escapeHtml($userInput) ?>
 */
if (! function_exists('escapeHtml')) {
    function escapeHtml(string $string): string
    {
        return Escaper::instance()->escapeHtml($string);
    }
}

/**
 * Escapes a string for HTML attribute output
 *
 * Used when outputting user-contributed content to an
 * on attribute value of an HTML element.
 *
 * Example:
 * <input type="text" value="<?= escapeHtmlAttr($userInput) ?>">
 */
if (! function_exists('escapeHtmlAttr')) {
    function escapeHtmlAttr(string $string): string
    {
        return Escaper::instance()->escapeHtmlAttr($string);
    }
}

/**
 * Escapes a string for JavaScript output
 *
 * Used when outputting user-contributed content within
 * a <script> body.
 *
 * Example:
 * <script>
 *   var userInput = "<?= escapeJs($userInput) ?>";
 * </script>
 */
if (! function_exists('escapeJs')) {
    function escapeJs(string $string): string
    {
        return Escaper::instance()->escapeJs($string);
    }
}

/**
 * Escapes a string for CSS output
 *
 * Used when outputting user-contributed content within
 * a <style> tag.
 *
 * Example:
 * <style>
 *  .user-input {
 *     content: "<?= escapeCss($userInput) ?>";
 * }
 * </style>
 */
if (! function_exists('escapeCss')) {
    function escapeCss(string $string): string
    {
        return Escaper::instance()->escapeCss($string);
    }
}

/**
 * Escapes a string for URL output
 *
 * Used when outputting user-contributed content within
 * a URL.
 *
 * Example:
 * <a href="<?= escapeUrl($url) ?>">Link</a>
 */
if (! function_exists('escapeUrl')) {
    function escapeUrl(string $string): string
    {
        return Escaper::instance()->escapeUrl($string);
    }
}

/**
 * Gets the Response instance.
 *
 * Example:
 *  response()->withBody('Hello, World!')->send();
 */
if (! function_exists('response')) {
    function response(): Response
    {
        return Response::instance();
    }
}

/**
 * Gets the Validation\Factory instance.
 */
if (! function_exists('validation')) {
    function validation(): Factory
    {
        return Validation::instance();
    }
}

/**
 * Gets the View\Meta instance.
 */
if (! function_exists('viewMeta')) {
    function viewMeta(): Meta
    {
        return Meta::instance();
    }
}
