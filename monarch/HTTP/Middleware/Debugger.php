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
        // Attach Tracy to the end of the content if it's enabled
        if (DEBUG && $request->isHtmx()) {
            TracyDebugger::dispatch();
            $response->withHeader(new Header('X-Tracy-Ajax', '1'));
        }

        return $next($request, $response);
    }
}
