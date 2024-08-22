# Middleware

Middleware is simply code that can run either before or after a request is processed. It can be used to modify the request or response, or to perform some other action, such as rate-limiting, logging, or authentication.

## Writing Middleware

Middleware are created as invokable classes that implement the `__invoke` method. The `__invoke` method receives the request, response, and the next middleware in the pipeline as arguments. The middleware can then modify the request or response, and optionally call the next middleware in the pipeline.

Here is an example of a simple middleware that checks if a user is authenticated:

```php
class LoggerMiddleware
{
    public function __invoke($request, $response, $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()
                ->withStatus(401);
        }

        // Call the next middleware in the pipeline
        return $next($request, $response);
    }
}
```

As seen the middleware chain can be broken by returning a response object from the middleware. This is useful for implementing authentication, rate limiting, or other checks that should prevent the request from being processed.

Your actions can happen either before the next middleware is called, or after. For example, you could log the request before the next middleware is called, or log the response after the next middleware is called.

```php
class LoggerMiddleware
{
    public function __invoke($request, $response, $next)
    {
        // Log the request before the request is processed
        Log::info('Request received: ' . $request->url());

        // Call the next middleware in the pipeline
        $response = $next($request, $response);

        // Log the response after the request is processed
        Log::info('Response sent: ' . $response->status());

        return $response;
    }
}
```

## Registering Middleware

Middleware can be registered in the `config/middleware.php` file. Middleware is grouped together under an alias. The config class must contain a key with the group name that contains an array of middleware classes that should be run on that request. The order of the middleware in the array is the order in which they will be run.

```php
return [
    // The 'web' group
    'web' => [
        App\Middleware\LoggerMiddleware::class,
        App\Middleware\AuthMiddleware::class,
    ],
];
```

A default group named `web` is provided that is run on every request. You can create additional groups by adding a new key to the config file.

```php
return [
    // A 'web' group
    'web' => [
        App\Middleware\LoggerMiddleware::class,
        App\Middleware\AuthMiddleware::class,
    ],

    // An 'api' group
    'api' => [
        App\Middleware\RateLimitMiddleware::class,
    ],
];
```

## Specifying Middleware

By default, the `web` group is run on every request. You can specify which group of middleware should be run on a route by using the `middleware` method on the route's control file, if one exists. It takes HTTP request verb as the only argument.

The `middleware` method should return the name of the middleware group that should be run on the request, or an array of middleware classes that should be run on the request.

```php
// routes/products.control.php
<?php

return new class()
{
    public function get()
    {
        //
    }

    public function post()
    {
        //
    }

    public function middleware(string $method)
    {
        return match ($method) {
            'post' => [
                ...config('middleware.web'),
                App\Middleware\LoggerMiddleware::class,
                App\Middleware\AuthMiddleware::class,
            ],
            default => 'web',
        };
    }
}
```

## Skeleton File

The following is a skeleton middleware file that can be copied and pasted into your project:

```php
<?php

namespace App\Middleware;

class SkeletonMiddleware
{
    public function __invoke($request, $response, $next)
    {
        //

        return $next($request, $response);
    }
}
```
