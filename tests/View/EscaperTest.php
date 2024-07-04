<?php

use Monarch\View\Escaper;

test('escape html', function () {
    $escaper = Escaper::instance();
    $input = '<script>alert("Hello, World!")</script>';
    $expectedOutput = '&lt;script&gt;alert(&quot;Hello, World!&quot;)&lt;/script&gt;';

    expect($escaper->escapeHtml($input))->toEqual($expectedOutput);
    expect(escapeHtml($input))->toEqual($expectedOutput);
});

test('escape html attr', function () {
    $escaper = Escaper::instance();
    $input = 'John "Doe"';
    $expectedOutput = 'John&#x20;&quot;Doe&quot;';

    expect($escaper->escapeHtmlAttr($input))->toEqual($expectedOutput);
    expect(escapeHtmlAttr($input))->toEqual($expectedOutput);
});

test('escape js', function () {
    $escaper = Escaper::instance();
    $input = 'console.log("Hello, World!");';
    $expectedOutput = 'console.log\x28\x22Hello,\x20World\x21\x22\x29\x3B';

    expect($escaper->escapeJs($input))->toEqual($expectedOutput);
    expect(escapeJs($input))->toEqual($expectedOutput);
});

test('escape css', function () {
    $escaper = Escaper::instance();
    $input = 'font-family: "Arial", sans-serif;';
    $expectedOutput = 'font\2D family\3A \20 \22 Arial\22 \2C \20 sans\2D serif\3B ';

    expect($escaper->escapeCss($input))->toEqual($expectedOutput);
    expect(escapeCss($input))->toEqual($expectedOutput);
});

test('escape url', function () {
    $escaper = Escaper::instance();
    $input = 'Hello, World!';
    $expectedOutput = 'Hello%2C%20World%21';

    expect($escaper->escapeUrl($input))->toEqual($expectedOutput);
    expect(escapeUrl($input))->toEqual($expectedOutput);
});
