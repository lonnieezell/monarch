<?php

use Monarch\View\Renderers\HTMLRenderer;
use Monarch\View\Renderers\MarkdownRenderer;

return [
    'renderers' => [
        '.api.php' => '',
        '.php' => HTMLRenderer::class,
        '.md' => MarkdownRenderer::class,
    ],
];
