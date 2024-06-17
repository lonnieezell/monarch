<?php

namespace Monarch\HTTP;

class Middleware
{
    private static self $instance;

    private function __construct(
        private Request $request,
        private array $middleware = []
    ) {
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
    public function forControl(?object $control = null): self
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

        $this->middleware = is_array($middleware)
            ? $middleware
            : [];

        return $this;
    }

    /**
     * Set the middleware classes to be processed.
     */
    public function setMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Get the middleware classes to be processed.
     */
    public function middleware(): array
    {
        return $this->middleware;
    }

    /**
     * Process all middleware classes, breaking if a response is returned.
     */
    public function process(Request $request, Response $response, callable $next)
    {
        $chain = array_reduce(
            // Middleware classes
            array_reverse($this->middleware),
            // Callback
            function ($next, $middleware) {
                if (is_string($middleware)) {
                    $middleware = new $middleware();
                }

                return fn ($request, $response) => $middleware->handle($request, $response, $next);
            },
            // Initial value
            $next
        );

        return $chain($request, $response);
    }
}
