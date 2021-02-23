<?php

/**
 * Setup Kint
 */
Kint::$aliases[] = 'dd';
Kint::$expanded = true;
Kint\Renderer\RichRenderer::$theme = 'aante-light.css';

function dd(...$vars)
{
	Kint::dump(...$vars);
	exit;
}

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
 * @return mixed
 */
function config(string $key)
{
    return \Myth\Config::factory()->get($key);
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
 * @param array $indexes
 * @param array $array
 *
 * @return mixed|null
 */
function _array_search_dot(array $indexes, array $array)
{
    // Grab the current index
    $currentIndex = $indexes
        ? array_shift($indexes)
        : null;

    if ((empty($currentIndex) && (int) $currentIndex !== 0) || (! isset($array[$currentIndex]) && $currentIndex !== '*'))
    {
        return null;
    }

    // Handle Wildcard (*)
    if ($currentIndex === '*')
    {
        // If $array has more than 1 item, we have to loop over each.
        foreach ($array as $value)
        {
            $answer = _array_search_dot($indexes, $value);

            if ($answer !== null)
            {
                return $answer;
            }
        }

        // Still here after searching all child nodes?
        return null;
    }

    // If this is the last index, make sure to return it now,
    // and not try to recurse through things.
    if (empty($indexes))
    {
        return $array[$currentIndex];
    }

    // Do we need to recursively search this value?
    if (is_array($array[$currentIndex]) && $array[$currentIndex])
    {
        return _array_search_dot($indexes, $array[$currentIndex]);
    }

    // Otherwise we've found our match!
    return $array[$currentIndex];
}

/**
 * Get a value from the environment, or return the default value.
 *
 * @param string      $key
 * @param string|null $default
 *
 * @return array|false|string|null
 */
function env(string $key, ?string $default)
{
    return getenv($key) ?? $default;
}

/**
 * View Service
 */
function view()
{
	return \Myth\View::factory();
}

