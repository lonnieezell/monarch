<?php

use Monarch\View\Renderers\APIRenderer;
use Monarch\View\Renderers\HTMLRenderer;
use Monarch\View\Renderers\MarkdownRenderer;

return [
    'renderers' => [
        '.api.php' => APIRenderer::class,
        '.php' => HTMLRenderer::class,
        '.md' => MarkdownRenderer::class,
    ],
];
