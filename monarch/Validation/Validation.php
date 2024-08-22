<?php

 declare(strict_types=1);

namespace Monarch\Validation;

use Closure;
use ReflectionException;
use Somnambulist\Components\Validation\Factory;

/**
 * Provides a wrapper around the third-party validation library to
 * make integrating it into your applications simpler.
 *
 * @see https://github.com/somnambulist-tech/validation
 */
class Validation
{
    private static ?Factory $factory = null;

    /**
     * Get the validation factory instance
     *
     * @return Factory
     */
    public static function instance(): Factory
    {
        if (null === self::$factory) {
            self::$factory = new Factory();

            self::registerCustomRules();
        }

        return self::$factory;
    }

    /**
     * Register custom validation rules that are defined
     * in the config/app::$validationRules array.
     */
    private static function registerCustomRules(): void
    {
        $rules = config('app.validationRules');

        if (!is_array($rules) || $rules === []) {
            return;
        }

        foreach ($rules as $name => $class) {
            if ($class instanceof Closure) {
                self::$factory->addRule($name, $class());
                continue;
            }

            self::$factory->addRule($name, new $class());
        }
    }

    /**
     * Uses reflection to get the list of rules from
     * the validation factory.
     *
     * Intended for use with testing.
     */
    public static function getRules(): array
    {
        // If no factory instance has been created, create it.
        if (null === self::$factory) {
            self::instance();
        }

        // User reflection to make the private property accessible
        $reflection = new \ReflectionClass(self::$factory);
        $property = $reflection->getProperty('rules');
        $property->setAccessible(true);

        return $property->getValue(self::$factory);
    }

    public static function reset(): void
    {
        self::$factory = null;
    }
}
