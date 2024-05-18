<?php

use Monarch\HTTP\Request;
use Monarch\Routes\Router;

describe('api', function () {
    test('GET method returns data', function () {
        $request = Request::createFromArray([
            'uri' => '/api',
            'method' => 'get',
            'scheme' => 'http',
            'host' => 'example.com',
            'port' => 80,
            'path' => '/api',
            'query' => [],
            'body' => '',
            'headers' => []
        ]);

        $router = Router::createWithBasePath(TESTPATH . '_support/routes');

        [$routeFile, $controlFile, $params] = $router->getFilesForRequest($request);
    });
});
