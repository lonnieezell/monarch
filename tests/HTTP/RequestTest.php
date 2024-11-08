<?php

use Monarch\HTTP\Request;

describe('create from globals', function () {
    test('returns a request instance', function () {
        $_SERVER['REQUEST_URI'] = 'https://example.com/foo/bar';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['bar' => 'baz'];

        $request = Request::createFromGlobals();

        expect($request)->toBeInstanceOf(Request::class);
        expect($request->uri)->toEqual('https://example.com/foo/bar');
        expect($request->method)->toEqual('GET');
        expect($request->query)->toEqual(['bar' => 'baz']);
        expect($request->scheme)->toEqual('https');
        expect($request->host)->toEqual('example.com');
        expect($request->port)->toEqual(80);
        expect($request->path)->toEqual('foo/bar');
        expect($request->body)->toEqual('');
        expect($request->headers)->toEqual([
            'Content-Type' => 'text/html',
        ]);
    });
});

describe('create from array', function () {
    test('returns a request instance', function () {
        $data = [
            'method' => 'GET',
            'uri' => 'example',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer token',
            ],
            'body' => '{"name": "John"}',
        ];

        $request = Request::createFromArray($data);

        expect($request)->toBeInstanceOf(Request::class);
        expect($request->method)->toEqual('GET');
        expect($request->uri)->toEqual('example');
        expect($request->headers)->toEqual([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token',
        ]);
        expect($request->body)->toEqual('{"name": "John"}');
    });
});

describe('to array', function () {
    test('returns defaults from empty request', function () {
        $request = new Request();

        $expected = [
            'uri' => '',
            'method' => 'GET',
            'scheme' => 'http',
            'host' => '',
            'port' => 80,
            'path' => '',
            'query' => [],
            'body' => '',
            'headers' => [],
        ];

        expect($request->toArray())->toEqual($expected);
    });

    test('returns all values from request', function () {
        $request = Request::createFromArray([
            'uri' => 'example',
            'method' => 'POST',
            'scheme' => 'https',
            'host' => 'example.com',
            'port' => 443,
            'path' => '/foo/bar',
            'query' => ['bar' => 'baz'],
            'body' => '{"name": "John"}',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer token',
            ],
        ]);

        $expected = [
            'uri' => 'example',
            'method' => 'POST',
            'scheme' => 'https',
            'host' => 'example.com',
            'port' => 443,
            'path' => '/foo/bar',
            'query' => ['bar' => 'baz'],
            'body' => '{"name": "John"}',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer token',
            ],
        ];

        expect($request->toArray())->toEqual($expected);
    });
});

describe('has header', function () {
    test('returns true if header exists', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->hasHeader('Content-Type'))->toBeTrue();
        expect($request->hasHeader('Authorization'))->toBeFalse();
    });

    test('returns false with no headers', function () {
        $request = Request::createFromArray([]);

        expect($request->hasHeader('Content-Type'))->toBeFalse();
    });
});

test('returns header value', function () {
    $request = Request::createFromArray([
        'headers' => [
            'Content-Type' => 'application/json',
        ]
    ]);

    expect($request->header('Content-Type'))->toEqual('application/json');
    expect($request->header('Authorization'))->toBeNull();
});

describe('is htmx request', function () {
    test('is htmx request true', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Request' => 'true',
            ]
        ]);

        expect($request->isHtmx())->toBeTrue();
    });

    test('is htmx request false', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->isHtmx())->toBeFalse();
    });
});

describe('is boosted request', function () {
    test('is boosted request true', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Boosted' => 'true',
            ]
        ]);

        expect($request->isBoosted())->toBeTrue();
    });

    test('is boosted request false', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Boosted' => 'anything else',
            ]
        ]);

        expect($request->isBoosted())->toBeFalse();
    });

    test('is boosted request not present', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->isBoosted())->toBeFalse();
    });
});

describe('prompt', function () {
    test('returns prompt value', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Prompt' => 'Are you sure?',
            ]
        ]);

        expect($request->prompt())->toEqual('Are you sure?');
    });

    test('returns null if prompt not present', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->prompt())->toBeNull();
    });
});

describe('target', function () {
    test('returns target value', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Target' => 'target-id',
            ]
        ]);

        expect($request->target())->toEqual('target-id');
    });

    test('returns null if target not present', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->target())->toBeNull();
    });
});

describe('trigger id', function () {
    test('returns trigger id value', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Trigger' => 'trigger-id',
            ]
        ]);

        expect($request->triggerId())->toEqual('trigger-id');
    });

    test('returns null if trigger id not present', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->triggerId())->toBeNull();
    });
});

describe('trigger name', function () {
    test('returns trigger name value', function () {
        $request = Request::createFromArray([
            'headers' => [
                'HX-Trigger-Name' => 'trigger-name',
            ]
        ]);

        expect($request->triggerName())->toEqual('trigger-name');
    });

    test('returns null if trigger name not present', function () {
        $request = Request::createFromArray([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        expect($request->triggerName())->toBeNull();
    });
});
