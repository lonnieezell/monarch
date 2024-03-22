<?php

use Myth\HTTP\Request;
use Myth\Routes\Router;
use Myth\Routes\Renderer;

include '../myth/bootstrap.php';

ob_start();

$request = Request::createFromGlobals();

$router = new Router();
$router->setBasePath(ROOTPATH .'routes');
[$routeFile, $controlFile] = $router->getFilesForRequest($request);

// Defaults
$content = 'index';
$data = [];

$control = $controlFile !== null ? include $controlFile : null;

if ($control && method_exists($control, strtolower($request->method))) {
    $output = $control->{strtolower($request->method)}();

    if (is_array($output)) {
        $content = $output['content'] ?? $content;
        $data = $output['data'] ?? $output;
    } elseif (is_string($output)) {
        $content = $output;
    }
}


//---------------------------------------------------------
// LOAD THE ROUTE FILE
//---------------------------------------------------------
$renderer = new Renderer();
$output = $renderer
    ->withRouteParams(content: $content, data: $data)
    ->render($routeFile);

echo $output;

// //---------------------------------------------------------
// // REPLACE PERFORMANCE DATA IN VIEW
// //---------------------------------------------------------
// $elapsed = round(microtime(true) - START_TIME, 4);

// $output = str_replace('{elapsed_time}', $elapsed, $output);
// $output = str_replace('{memory_usage}', round(memory_get_peak_usage() / 1024 / 1024, 2), $output);

// echo $output;
