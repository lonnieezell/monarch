<?php

namespace Myth;

use Myth\HTTP\Request;
use Myth\Routes\Router;
use Myth\View\Renderer;

class App
{
    private static App $instance;
    private Request $request;

    public readonly float $startTime;

    public static function instance(): App
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->startTime =  microtime(true);

        // constants
        define('ROOTPATH', realpath('..') .'/');
        define('APPPATH', realpath(ROOTPATH.'app') .'/');
        define('TESTPATH', realpath(ROOTPATH.'tests') .'/');

        // Default timezone of server
        date_default_timezone_set('UTC');
    }

    private function boot()
    {
        // Load .env file
        (new \Myth\DotEnv(ROOTPATH .'/.env'))->load();

        include ROOTPATH .'myth/helpers/common.php';
    }

    public function run()
    {
        $this->boot();

        ob_start();

        $this->request = Request::createFromGlobals();

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
    }
}
