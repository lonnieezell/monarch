<?php

declare(strict_types=1);

namespace Monarch;

use Monarch\Helpers\Numbers;
use Monarch\HTTP\Middleware;
use Monarch\HTTP\Request;
use Monarch\HTTP\Response;
use Monarch\Routes\Router;
use Throwable;

class App
{
    private static App $instance;

    public readonly Request $request;
    public readonly float $startTime;

    public static function createFromGlobals(): App
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->setRequest(Request::createFromGlobals());
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->startTime =  microtime(true);

        // Default timezone of server
        date_default_timezone_set('UTC');
    }

    /**
     * Runs the application and returns the output.
     */
    public function run()
    {
        $this->prepareEnvironment();
        $this->setupSession();

        ob_start();

        // Get the control file, if exists
        $router = new Router();
        $router->setBasePath(ROOTPATH .'routes');
        $route = $router->getRouteForRequest($this->request);

        /** @var object */
        $control = $route->controlFile !== '' ? include $route->controlFile : null;

        $action = fn (Request $request, Response $response) => $router->display(
            request: $request,
            control: $control,
            route: $route
        );

        // Run the middleware
        $request = $this->request;
        $response = Response::createFromRequest($request);
        $html =  Middleware::forRequest($request)
                ->forControl($control)
                ->process($request, $response, $action);

        $response->withBody($html);
        $response->withBody($this->replacePerformanceMarkers($response->body()));

        return $response->send();
    }

    public function processMiddleware(Request $request, Response $response, array $middleware)
    {
        $action = fn (Request $request, Response $response) => $middleware($request, $response);

        // Run the middleware
        $middleware = Middleware::forRequest($request);
        $response = Response::createFromRequest($request);

        foreach ($middleware as $class) {
            $action = fn ($request) => new $class($request, $response, $action);

            if ($action instanceof Response) {
                break;
            }
        }

        return $action($request);
    }

    public function prepareEnvironment()
    {
        // Load .env file
        (new DotEnv(ROOTPATH .'/.env'))->load();

        include ROOTPATH .'monarch/Helpers/common.php';
    }

    /**
     * Sets the Request instance for the app.
     * This method is primarily used internally for
     * the `createFromGlobals` method, but is also
     * useful for testing.
     *
     * Example:
     *  $request = new Request();
     *  $app = App::instance();
     *  $app->setRequest($request);
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    private function replacePerformanceMarkers(string $output): string
    {
        if (! defined('START_TIME')) {
            return $output;
        }

        // Execution time
        $elapsed = microtime(true) - START_TIME;
        $output = str_replace('{elapsed_time}', Numbers::humanTime($elapsed), $output);

        // Memory usage
        $peakMemory = Numbers::humanSize(memory_get_peak_usage());
        $currentMemory = Numbers::humanSize(memory_get_usage());
        $output = str_replace('{memory_usage}', "{$currentMemory}/{$peakMemory}", $output);

        return $output;
    }

    private function setupSession()
    {
        $handler = config('app.sessionHandler');
        $savePath = config('app.sessionSavePath');

        ini_set('session.save_handler', $handler);

        if ($handler !== 'files') {
            ini_set('session.save_path', $savePath);
        }
    }
}
