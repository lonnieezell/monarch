<?php

namespace Myth;

use Myth\HTTP\Request;
use Myth\Routes\Router;
use Myth\View\Renderer;
use RuntimeException;
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

            $router = new Router();
            $router->setBasePath(ROOTPATH .'routes');
            [$routeFile, $controlFile] = $router->getFilesForRequest($this->request);

            // Defaults
            $content = 'index';
            $data = [];

            $control = $controlFile !== null ? include $controlFile : null;

            if ($control && method_exists($control, strtolower($this->request->method))) {
                $output = $control->{strtolower($this->request->method)}();

                if (is_array($output)) {
                    $content = $output['content'] ?? $content;
                    $data = $output['data'] ?? $output;
                } elseif (is_string($output)) {
                    $content = $output;
                }
            }

            $renderer = Renderer::createWithRequest($this->request);
            $output = $renderer
                ->withRouteParams(content: $content, data: $data)
                ->render($routeFile);

            return $output;
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function prepareEnvironment()
    {
        // Load .env file
        (new \Myth\DotEnv(ROOTPATH .'/.env'))->load();

        include ROOTPATH .'myth/helpers/common.php';
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

    private function handleException(Throwable $e)
    {
        if (ENVIRONMENT === 'testing') {
            throw $e;
        }

        $type = get_class($e);
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
