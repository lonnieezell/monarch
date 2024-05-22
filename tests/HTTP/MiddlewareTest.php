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

describe('Middleware', function () {
    it('should return the default middleware if no control file is provided', function () {
        $middleware = Middleware::forRequest($this->request)->forControl();

        expect($middleware)->toBeArray();
        expect($middleware)->toMatchArray(config('middleware.web'));
    });

    it('should return the default middleware if the control file does not have a middleware method', function () {
        $control = new class () {
            //
        };

        $middleware = Middleware::forRequest($this->request)->forControl($control);

        expect($middleware)->toBeArray();
        expect($middleware)->toMatchArray(config('middleware.web'));
    });

    it('should return the default middleware if the control file does not return a middleware group name', function () {
        $control = new class () {
            function middleware(string $method) : string|array
            {
                return '';
            }
        };

        $middleware = Middleware::forRequest($this->request)->forControl($control);

        expect($middleware)->toBeArray();
        expect($middleware)->toMatchArray(config('middleware.web'));
    });

    it('should return the middleware for the given group name', function () {
        $control = new class () {
            function middleware(string $method) : string|array
            {
                return 'api';
            }
        };

        $middleware = Middleware::forRequest($this->request)->forControl($control);

        expect($middleware)->toBeArray();
        expect($middleware)->toMatchArray(config('middleware.api'));
    });
});