<?php

namespace Monarch\Helpers;

use ReflectionException;
use ReflectionProperty;

class Reflection
{
    /**
     * Allow access to a protected or private property on an object
     *
     * Example:
     *  $property = Reflection::allowPropertyAccess($object, 'privateVar');
     *  $property->setValue($object, 'new value');
     *
     * @throws ReflectionException
     */
    public static function allowPropertyAccess(mixed $object, string $property): ReflectionProperty
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Get a protected or private property's value from an object
     *
     * Example:
     *  $privateVar = Reflection::getProperty($object, 'privateVar');
     *
     * @throws ReflectionException
     */
    public static function getProperty(mixed $object, string $property): mixed
    {
        $reflectedProperty = self::allowPropertyAccess($object, $property);

        return $reflectedProperty->getValue($object);
    }

    /**
     * Set a protected or private property's value on an object
     *
     * Example:
     *  Reflection::setProperty($object, 'privateVar', 'new value');
     *
     * @throws ReflectionException
     */
    public static function setProperty(mixed $object, string $property, mixed $value): void
    {
        $reflectedProperty = self::allowPropertyAccess($object, $property);
        $reflectedProperty->setValue($object, $value);
    }

    /**
     * Call a protected or private method on an object
     *
     * Example:
     *  $result = Reflection::callMethod($object, 'privateMethod', [$arg1, $arg2]);
     *
     * @throws ReflectionException
     */
    public static function callMethod(mixed $object, string $method, array $args = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
