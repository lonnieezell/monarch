<?php

use Monarch\HTTP\Middleware;
use Monarch\HTTP\Request;

beforeEach(function () {
    $this->request = Request::createFromArray([
        'method' => 'GET',
        'uri' => '/test',
        'headers' => [],
        'body' => '',
    ]);
});

it('should process the middleware classes', function () {
    $middleware = Middleware::forRequest($this->request)
        ->process($this->request, new Monarch\HTTP\Response(), function ($request, $response) {
            return $response->withStatus(201)->withBody('Hello, world!');
        });

    expect($middleware)->toBeInstanceOf(Monarch\HTTP\Response::class);
    expect($middleware->status())->toBe(201);
});

it('should process the middleware classes with a control', function () {
    $control = new class () {
        public function middleware($method)
        {
            return [
                Tests\_support\Middleware\BodyPrinter::class,
            ];
        }
    };

    $middleware = Middleware::forRequest($this->request)
        ->forControl($control)
        ->process($this->request, new Monarch\HTTP\Response(), function ($request, $response) {
            return $response->withStatus(201);
        });

    expect($middleware)->toBeInstanceOf(Monarch\HTTP\Response::class);
    // Initial response should update the status.
    expect($middleware->status())->toBe(201);
    // The middleware should update the body.
    expect($middleware->body())->toBe('Hello, world!');
});
