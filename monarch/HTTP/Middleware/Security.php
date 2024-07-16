<?php

namespace Monarch\HTTP\Middleware;

use Closure;
use Monarch\HTTP\CSRF;
use Monarch\HTTP\Header;
use Monarch\HTTP\Request;
use Monarch\HTTP\Response;

class Security
{
    public function handle(Request $request, Response $response, Closure $next)
    {
        // Verify the CSRF token
        if ($request->method === 'POST') {
            $token = $request->body['csrf_token'] ?? '';

            if (! CSRF::verify($token)) {
                $response->withStatus(403);
                $response->withHeader(new Header('Content-Type', 'text/plain'));
                $response->withBody('Invalid CSRF token');

                return $response;
            }
        }

        $html = $next($request, $response);

        return $html;
    }
}
