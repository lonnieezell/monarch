<?php

declare(strict_types=1);

namespace Monarch\Concerns;

trait IsSingleton
{
    private static $instance;

    /**
     * Generates a new instance of the class if one does not exist.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets the instance of the class that should be
     * returned by the instance() method.
     */
    public static function setInstance($instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Resets the instance of the class.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
