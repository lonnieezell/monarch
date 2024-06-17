<?php

namespace Tests\_support\Middleware;

class BodyPrinter
{
    public function handle($request, $response, $next)
    {
        $response->withBody('Hello, world!');

        return $next($request, $response);
    }
}
