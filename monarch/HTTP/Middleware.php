<?php

namespace Monarch\HTTP;

class Middleware
{
    private static self $instance;

    private function __construct(private Request $request)
    {
        //
    }

    /**
     * Grab a singleton instance of the class.
     */
    public static function forRequest(Request $request): Middleware
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($request);
        }

        return self::$instance;
    }

    /**
     * Gets the middleware for the given route control file.
     *
     * If not control file exists, it will return the default middleware.
     *
     * If the control returns a string with a middleware group name,
     * it will return the middleware for that group.
     */
    public function forControl(?object $control = null): array
    {
        $middleware = is_object($control) && method_exists($control, 'middleware')
            ? $control?->middleware($this->request->method)
            : null;

        if (empty($middleware)) {
            $middleware = config('middleware.default');
        }

        if (is_string($middleware)) {
            $middleware = config("middleware.{$middleware}");
        }

        return is_array($middleware)
            ? $middleware
            : [];
    }
}
