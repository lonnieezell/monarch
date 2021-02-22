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
 * Get a value from the environment, or return the default value.
 *
 * @param string      $key
 * @param string|null $default
 *
 * @return array|false|string|null
 */
function env(string $key, ?string $default) {
    return getenv($key) ?? $default;
}

/**
 * View Service
 */
function view() {
	return \Myth\View::factory();
}

