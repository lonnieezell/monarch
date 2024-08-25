<?php

namespace Monarch\HTTP\Middleware;

use Closure;
use Monarch\Debug;
use Monarch\HTTP\Request;
use Monarch\HTTP\Response;

class Debugger
{
    public function handle(Request $request, Response $response, Closure $next)
    {
        $html = $next($request, $response);

        // If we're not an htmx request, then we need to insert the monarch debugger
        if (DEBUG && !$request->isHtmx()) {
            $html = $this->insertDebugger($html);
        }

        return $html;
    }

    private function insertDebugger(string $html)
    {
        return str_replace('</body>', debug()->reportLogs() . '</body>', $html);
    }
}
