<?php

namespace Monarch\HTTP\Middleware;

use Closure;
use Monarch\HTTP\Header;
use Monarch\HTTP\Request;
use Monarch\HTTP\Response;
use Tracy\Debugger as TracyDebugger;

class Debugger
{
    public function handle(Request $request, Response $response, Closure $next)
    {
        // Ensure Tracy can track AJAX requests
        if (DEBUG && $request->isHtmx()) {
            TracyDebugger::dispatch();
            $response->withHeader(new Header('X-Tracy-Ajax', '1'));
        }

        $html = $next($request, $response);

        return $html;
    }
}
