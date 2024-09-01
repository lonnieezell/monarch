<?php

declare(strict_types=1);

namespace Monarch\Concerns;

trait IsSingleton
{
    private static $instance;

    public static function instance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
