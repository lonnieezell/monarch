<?php

use Monarch\Validation\Validation;
use Somnambulist\Components\Validation\Factory;
use Tests\Support\Validation\ValidRule;

describe('Validation', function () {
    beforeEach(function () {
        // Reset the validation factory before each test
        Validation::reset();
    });

    it('should return an instance of the validation factory', function () {
        $factory = Validation::instance();

        expect($factory)->toBeInstanceOf(Factory::class);
    });

    it('should register custom validation rules', function () {
        // Mock the config/app::$validationRules array
        config()->mock('app', [
            'validationRules' => [
                'is_valid' => ValidRule::class,
            ]
        ]);

        $rules = Validation::getRules();

        expect($rules)->toHaveKey('is_valid');
        expect($rules['is_valid'])->toBeInstanceOf(ValidRule::class);
    });
});
