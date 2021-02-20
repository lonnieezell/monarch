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
 * View Service
 */
function view() {
	return \Myth\View::factory();
}
