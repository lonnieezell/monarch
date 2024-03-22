<?php

use Myth\HTTP\Request;
use Myth\Routes\Router;

describe('router', function () {
    test('can set base path', function () {
        $router = Router::createWithBasePath(TESTPATH .'_support/routes');

        expect($router->basePath)->toBe(TESTPATH .'_support/routes/');
    });

    test('can get files for request', function () {
        $request = Request::createFromArray([
            'uri' => '/foo/bar',
            'method' => 'get',
            'scheme' => 'http',
            'host' => 'example.com',
            'port' => 80,
            'path' => '/foo/bar',
            'query' => [],
            'body' => '',
            'headers' => []
        ]);

        $router = Router::createWithBasePath(TESTPATH .'_support/routes');

        [$routeFile, $controlFile] = $router->getFilesForRequest($request);

        expect($routeFile)->toBe(TESTPATH . '_support/routes/foo/bar.php');
        expect($controlFile)->toBe(TESTPATH . '_support/routes/foo/bar.control.php');
    });
});
