<?php

use Monarch\Components\Attributes;

describe('Attributes', function () {
    it('should merge attributes correctly', function () {
        $attributes = new Attributes(['class' => 'text-red-500']);
        $attributes->merge(['class' => 'font-bold']);

        expect($attributes->get('class'))->toBe('font-bold text-red-500');
    });

    it('should include only specified attributes', function () {
        $attributes = new Attributes([
            'class' => 'text-red-500',
            'id' => 'my-element',
            'data-foo' => 'bar',
        ]);
        $attributes->only('class', 'id');

        expect((string)$attributes)->toBe('class="text-red-500" id="my-element"');
    });

    it('should exclude specified attributes', function () {
        $attributes = new Attributes([
            'class' => 'text-red-500',
            'id' => 'my-element',
            'data-foo' => 'bar',
        ]);
        $attributes->except('class', 'data-foo');

        expect((string)$attributes)->toBe('id="my-element"');
    });

    it('should check if attribute exists', function () {
        $attributes = new Attributes(['class' => 'text-red-500']);

        expect($attributes->has('class'))->toBe(true);
        expect($attributes->has('id'))->toBe(false);
    });

    it('should get attribute value', function () {
        $attributes = new Attributes(['class' => 'text-red-500']);

        expect($attributes->get('class'))->toBe('text-red-500');
        expect($attributes->get('id'))->toBeNull();
    });
});
