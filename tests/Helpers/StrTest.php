<?php

use Monarch\Helpers\Str;

describe('Str', function () {
    test('pascal from snake_case', function () {
        $input = 'hello_wOrLd';
        $expectedOutput = 'HelloWorld';

        $output = Str::pascal($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('pascal from kebab-case', function () {
        $input = 'hello-wOrLd';
        $expectedOutput = 'HelloWorld';

        $output = Str::pascal($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('pascal from camelCase', function () {
        $input = 'helloWorld';
        $expectedOutput = 'HelloWorld';

        $output = Str::pascal($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('camel from snake_case', function () {
        $input = 'hello_wOrLd';
        $expectedOutput = 'helloWorld';

        $output = Str::camel($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('camel from kebab-case', function () {
        $input = 'hello-wOrLd';
        $expectedOutput = 'helloWorld';

        $output = Str::camel($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('camel from PascalCase', function () {
        $input = 'HelloWorld';
        $expectedOutput = 'helloWorld';

        $output = Str::camel($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('kebab from snake_case', function () {
        $input = 'hello_wOrLd';
        $expectedOutput = 'hello-world';

        $output = Str::kebab($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('kebab from PascalCase', function () {
        $input = 'HelloWorld';
        $expectedOutput = 'hello-world';

        $output = Str::kebab($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('kebab from camelCase', function () {
        $input = 'helloWorld';
        $expectedOutput = 'hello-world';

        $output = Str::kebab($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('snake from kebab-case', function () {
        $input = 'hello-world';
        $expectedOutput = 'hello_world';

        $output = Str::snake($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('snake from PascalCase', function () {
        $input = 'HelloWorld';
        $expectedOutput = 'hello_world';

        $output = Str::snake($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('snake from camelCase', function () {
        $input = 'helloWorld';
        $expectedOutput = 'hello_world';

        $output = Str::snake($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('title from snake_case', function () {
        $input = 'hello_wOrLd';
        $expectedOutput = 'Hello World';

        $output = Str::title($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('title from kebab-case', function () {
        $input = 'hello-world';
        $expectedOutput = 'Hello World';

        $output = Str::title($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('title from camelCase', function () {
        $input = 'helloWorld';
        $expectedOutput = 'Hello World';

        $output = Str::title($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from snake_case', function () {
        $input = 'hello_wOrLd';
        $expectedOutput = 'hello-world';

        $output = Str::slug($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from kebab-case', function () {
        $input = 'hello-world';
        $expectedOutput = 'hello-world';

        $output = Str::slug($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from camelCase', function () {
        $input = 'helloWorld';
        $expectedOutput = 'hello-world';

        $output = Str::slug($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from title case', function () {
        $input = 'Hello World';
        $expectedOutput = 'hello-world';

        $output = Str::slug($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from title case with custom divider', function () {
        $input = 'Hello World';
        $expectedOutput = 'hello_world';

        $output = Str::slug($input, '_');

        expect($output)->toEqual($expectedOutput);
    });

    test('slug from title case with custom divider and non-ASCII characters', function () {
        $input = 'Hello World ñ';
        $expectedOutput = 'hello-world-n';

        $output = Str::slug($input);

        expect($output)->toEqual($expectedOutput);
    });

    test('contains', function () {
        $input = 'hello world';
        $needle = 'world';

        $output = Str::contains($input, $needle);

        expect($output)->toBeTrue();
    });

    test('contains with non-existent needle', function () {
        $input = 'hello world';
        $needle = 'earth';

        $output = Str::contains($input, $needle);

        expect($output)->toBeFalse();
    });

    test('contains with empty needle', function () {
        $input = 'hello world';
        $needle = '';

        $output = Str::contains($input, $needle);

        expect($output)->toBeTrue();
    });

    test('contains with empty haystack', function () {
        $input = '';
        $needle = 'world';

        $output = Str::contains($input, $needle);

        expect($output)->toBeFalse();
    });

    test('contains all', function () {
        $input = 'hello world';
        $needles = ['hello', 'world'];

        $output = Str::containsAll($input, $needles);

        expect($output)->toBeTrue();
        expect(Str::containsAll('hello world', ['hello', 'earth']))->toBeFalse();
    });

    test('length', function () {
        $input = 'hello world';

        $output = Str::length($input);

        expect($output)->toEqual(11);
    });

    test('length with non-ASCII characters', function () {
        $input = 'hello world ñ';

        $output = Str::length($input);

        expect($output)->toEqual(13);
    });

    test('length with empty string', function () {
        $input = '';

        $output = Str::length($input);

        expect($output)->toEqual(0);
    });

    test('limit', function () {
        $input = 'hello world';

        $output = Str::limit($input, 5);

        expect($output)->toEqual('hello...');
    });

    test('limit with custom end', function () {
        $input = 'hello world';

        $output = Str::limit($input, 5, '***');

        expect($output)->toEqual('hello***');
    });

    test('limit with limit greater than string length', function () {
        $input = 'hello world';

        $output = Str::limit($input, 20);

        expect($output)->toEqual('hello world');
    });

    test('limit with limit equal to string length', function () {
        $input = 'hello world';

        $output = Str::limit($input, 11);

        expect($output)->toEqual('hello world');
    });

    test('limit with limit less than 0', function () {
        $input = 'hello world';

        $output = Str::limit($input, -5);

        expect($output)->toEqual('...');
    });

    test('limit with non-ASCII characters', function () {
        $input = 'hello world ñ';

        $output = Str::limit($input, 5);

        expect($output)->toEqual('hello...');
    });

    test('random', function () {
        $output = Str::random(16);

        expect($output)->toMatch('/^[a-zA-Z0-9]{16}$/');
    });

    test('words', function () {
        $input = 'hello world';

        $output = Str::words($input, 1);

        expect($output)->toEqual('hello');
    });

    test('words with non-ASCII characters', function () {
        $input = 'hello world ñ';

        $output = Str::words($input, 2);

        expect($output)->toEqual('hello world');
    });

    test('words with limit greater than number of words', function () {
        $input = 'hello world';

        $output = Str::words($input, 5);

        expect($output)->toEqual('hello world');
    });
});
