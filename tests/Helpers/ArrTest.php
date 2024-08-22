<?php

declare(strict_types=1);

use Monarch\Helpers\Arr;

test('get', function () {
    $array = [
        'foo' => 'bar',
        'nested' => [
            'key' => 'value',
        ],
    ];

    expect(Arr::get($array, 'foo'))->toEqual('bar');
    expect(Arr::get($array, 'nested.key'))->toEqual('value');
    expect(Arr::get($array, 'nonexistent'))->toBeNull();
    expect(Arr::get($array, 'nonexistent', 'default'))->toEqual('default');
});

test('has', function () {
    $array = [
        'foo' => 'bar',
        'nested' => [
            'key' => 'value',
        ],
    ];

    expect(Arr::has($array, 'foo'))->toBeTrue();
    expect(Arr::has($array, 'nested.key'))->toBeTrue();
    expect(Arr::has($array, 'nonexistent'))->toBeFalse();
});

test('pluck', function () {
    $array = [
        ['name' => 'John', 'age' => 30],
        ['name' => 'Jane', 'age' => 29],
        ['name' => 'Doe', 'age' => 40],
    ];

    expect(Arr::pluck($array, 'name'))->toBeArray()->toEqual(['John', 'Jane', 'Doe']);
    expect(Arr::pluck($array, 'age'))->toBeArray()->toEqual([30, 29, 40]);

    $nestedArray = [
        ['user' => ['id' => 1, 'name' => 'John', 'age' => 30]],
        ['user' => ['id' => 2, 'name' => 'Jane', 'age' => 29]],
        ['user' => ['id' => 3, 'name' => 'Doe', 'age' => 40]],
    ];

    expect(Arr::pluck($nestedArray, 'user.name'))->toBeArray()->toEqual(['John', 'Jane', 'Doe']);
    expect(Arr::pluck($nestedArray, 'user.name', 'user.id'))->toBeArray()->toEqual([1 => 'John', 2 => 'Jane', 3 => 'Doe']);
});
