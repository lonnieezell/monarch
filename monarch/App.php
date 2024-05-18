<?php

declare(strict_types=1);

namespace Monarch;

use Monarch\Helpers\Numbers;
use Monarch\HTTP\Middleware;
use Monarch\HTTP\Request;
use Monarch\HTTP\Response;
use Monarch\Routes\Router;
use Monarch\View\Renderer;
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
        try {
            $this->prepareEnvironment();

            ob_start();

            // Get the control file, if exists
            $router = new Router();
            $router->setBasePath(ROOTPATH .'routes');
            [$routeFile, $controlFile, $routeParams] = $router->getFilesForRequest($this->request);

            /** @var object */
            $control = $controlFile !== '' ? include $controlFile : null;

            $action = fn (Request $request, Response $response) => $router->display(
                request: $request,
                routeFile: $routeFile,
                control: $control,
                routeParams: $routeParams
            );

            // Run the middleware
            $request = $this->request;
            $middleware = Middleware::forRequest($request)->forControl($control);
            $response = Response::createFromRequest($request);

            foreach ($middleware as $class) {
                $action = fn ($request) => $class($request, $response, $action);

                if ($action instanceof Response) {
                    break;
                }
            }

            $response->withBody($action($request, $response));
            $response->withBody($this->replacePerformanceMarkers($response->body()));

            return $response->send();
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
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

    private function handleException(Throwable $e)
    {
        if (ENVIRONMENT === 'testing') {
            throw $e;
        }

        $type = $e::class;
        $message = $e->getMessage();
        $code = $e->getCode();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        // TODO - Log the error
        // TODO - Used per-environment error pages
        // TODO - Hande HTTP status codes
        ob_start();
        include ROOTPATH .'routes/+error.php';
        echo ob_get_clean() ?? '';

        exit(1);
    }
}
