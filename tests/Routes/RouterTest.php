<?php

use Monarch\HTTP\Request;
use Monarch\Routes\Router;

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

        [$routeFile, $controlFile, $params] = $router->getFilesForRequest($request);

        expect($routeFile)->toBe(TESTPATH . '_support/routes/foo/bar.php');
        expect($controlFile)->toBe(TESTPATH . '_support/routes/foo/bar.control.php');
        expect($params)->toBeNull();
    });

    test('can get dynamic files for request', function () {
        $request = Request::createFromArray([
            'uri' => '/posts/2024/12',
            'method' => 'get',
            'scheme' => 'http',
            'host' => 'example.com',
            'port' => 80,
            'path' => '/posts/2024/12',
            'query' => [],
            'body' => '',
            'headers' => []
        ]);

        $router = Router::createWithBasePath(TESTPATH .'_support/routes');

        [$routeFile, $controlFile, $params] = $router->getFilesForRequest($request);

        expect($routeFile)->toBe(TESTPATH . '_support/routes/posts[year][month].php');
        expect($controlFile)->toBe(TESTPATH . '_support/routes/posts[year][month].control.php');
        expect($params)->toBe(['year' => '2024', 'month' => '12']);
    });
});
