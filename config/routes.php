<?php

use Myth\View\Renderers\HTMLRenderer;
use Myth\View\Renderers\MarkdownRenderer;

return [
    'renderers' => [
        '.api.php' => '',
        '.php' => HTMLRenderer::class,
        '.md' => MarkdownRenderer::class,
    ],
];
