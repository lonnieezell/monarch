<?php

namespace Monarch\Routes;

/**
 * Represents a single route in the application.
 */
class Route
{
    private array $attributes = [];

    public function __construct(
        public readonly string $routeFile,
        public readonly ?string $controlFile,
        public readonly ?array $params
    ) {
    }

    /**
     * Determines if the route has a control file.
     */
    public function hasControl(): bool
    {
        return $this->controlFile !== null && $this->controlFile !== '';
    }

    /**
     * Returns a single route parameter's value.
     * If the parameter does not exist, null is returned.
     */
    public function param(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}
