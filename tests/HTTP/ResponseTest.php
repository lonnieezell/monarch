<?php

use Monarch\HTTP\Response;
use Monarch\HTTP\Request;
use Monarch\HTTP\Cookie;
use Monarch\HTTP\Header;

describe('Response', function () {
    it('should create a response from a request', function () {
        $request = new Request();
        $response = Response::createFromRequest($request);

        expect($response)->toBeInstanceOf(Response::class);
        expect($response->status())->toBe(200);
    });

    it('should return the singleton instance', function () {
        $response1 = Response::instance();
        $response2 = Response::instance();

        expect($response1)->toBe($response2);
    });

    it('should set and get the status code', function () {
        $response = new Response();
        $response->withStatus(404);

        expect($response->status())->toBe(404);
    });

    it('should set and get the response body', function () {
        $response = new Response();
        $response->withBody('Hello, world!');

        expect($response->body())->toBe('Hello, world!');
    });

    it('should add and get response headers', function () {
        $response = new Response();
        $header1 = new Header('Content-Type', 'application/json');
        $header2 = new Header('X-Auth-Token', 'abc123');

        $response->withHeader($header1);
        $response->withHeader($header2);

        expect($response->headers())->toBe([$header1, $header2]);
    });

    it('should replace a response header', function () {
        $response = new Response();
        $header1 = new Header('Content-Type', 'application/json');
        $header2 = new Header('Content-Type', 'text/html');

        $response->withHeader($header1);
        $response->replaceHeader($header2);

        expect($response->headers())->toHaveCount(1);
        $headers = $response->headers();
        expect(array_shift($headers)->value)->toBe('text/html');
    });

    it('should remove a response header', function () {
        $response = new Response();
        $header1 = new Header('Content-Type', 'application/json');
        $header2 = new Header('X-Auth-Token', 'abc123');

        $response->withHeader($header1);
        $response->withHeader($header2);

        $response->forgetHeader('Content-Type');

        expect($response->headers())->toHaveCount(1);
        $headers = $response->headers();
        expect(array_shift($headers)->value)->toBe('abc123');
    });

    it('should add and get response cookies', function () {
        $response = new Response();
        $cookie1 = new Cookie('token', 'abc123');
        $cookie2 = new Cookie('session', 'xyz789');

        $response->withCookie($cookie1);
        $response->withCookie($cookie2);

        expect($response->cookies())->toBe([$cookie1, $cookie2]);
    });

    it('should remove a response cookie', function () {
        $response = new Response();
        $cookie1 = new Cookie('token', 'abc123');
        $cookie2 = new Cookie('session', 'xyz789');

        $response->withCookie($cookie1);
        $response->withCookie($cookie2);

        $response->forgetCookie('token');

        expect($response->cookies())->toHaveCount(1);
        $cookies = $response->cookies();
        expect(array_shift($cookies)->value)->toBe('xyz789');
    });

    it('should add and remove OOB swaps', function () {
        $response = new Response();
        $response->withBody('Hello, world!');

        $response->withSwap('swap1', 'value1');
        $response->withSwap('swap2', 'value2');

        expect($response->send())->toBe("Hello, world!\nvalue1\nvalue2");

        $response->forgetSwap('swap1');

        expect($response->send())->toBe("Hello, world!\nvalue2");
    });

    it('should prepend swaps before body tag', function () {
        $response = new Response();
        $response->withBody('<body>Hello, world!</body>');

        $response->withSwap('swap1', 'value1');

        expect($response->send())->toBe("<body>Hello, world!\nvalue1</body>");
    });
});
