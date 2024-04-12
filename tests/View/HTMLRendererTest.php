<?php

use Monarch\View\Renderer;
use Monarch\HTTP\Request;

describe('HTML renderer', function () {
    test('uses layouts root level', function () {
        $request = Request::createFromArray([
            'uri' => '/',
            'method' => 'get',
            'scheme' => 'http',
            'host' => 'example.com',
            'port' => 80,
            'path' => '/',
            'query' => [],
            'body' => '',
            'headers' => []
        ]);

        $renderer = Renderer::createWithRequest($request);
        $html = $renderer->render(TESTPATH . '_support/routes/index.php');

        expect($html)->toBeString();
        expect($html)->toContain('<h1>Layout 1</h1>');
    });

    test('uses nested layouts', function () {
        $request = Request::createFromArray([
            'uri' => '/',
            'method' => 'get',
            'scheme' => 'http',
            'host' => 'example.com',
            'port' => 80,
            'path' => '/',
            'query' => [],
            'body' => '',
            'headers' => []
        ]);

        $renderer = Renderer::createWithRequest($request);
        $html = $renderer->render(TESTPATH . '_support/routes/foo/bar.php');

        expect($html)->toBeString();
        expect($html)->toContain('<h1>Layout 1</h1>');
        expect($html)->toContain('<h2>Layout 2</h2>');
    });
});
