<?php

declare(strict_types=1);

namespace Monarch\Routes;

use Monarch\Config;
use Monarch\HTTP\Request;

/**
 * Given a URL and a base folder, this class will attempt to determine the
 * various files that make up our file-based routing system.
 */
class Router
{
    public readonly string $basePath;

    /**
     * Creates a new Router instance with a base path set.
     *
     * Example:
     *  $router = Router::createWithBasePath(__DIR__);
     */
    public static function createWithBasePath(string $path): static
    {
        $router = new static();
        $router->setBasePath($path);

        return $router;
    }

    /**
     * Sets the base path for the router to use when looking for route and
     * control files. This should be the full path to the folder that
     * contains the route and control files.
     *
     * Example;
     *  $router = new Router();
     *  $router->setBasePath(ROOTPATH .'routes');
     */
    public function setBasePath(string $path): static
    {
        $this->basePath = rtrim($path, '/ ') . '/';

        return $this;
    }

    /**
     * Given a request, will attempt to find the route and controller files.
     *
     * Example:
     *  $router = new Router();
     *  $router->setBasePath(ROOTPATH .'routes');
     *  [$routeFile, $controlFile] = $router->getFilesForRequest($request);
     */
    public function getFilesForRequest(Request $request): array
    {
        $path = ltrim($request->path ?: 'index', '/');
        $path = strtolower($path);
        $path = str_replace(['/', '.'], DIRECTORY_SEPARATOR, $path);

        $routeFile = null;
        $searchFile = $this->basePath . $path;

        // Handle when directory name matches route and has an index file.
        if (is_dir($searchFile)) {
            $searchFile .= '/index';
        }

        $controlFile = $this->basePath . $path . '.control.php';
        $availableRouteTypes = Config::factory()->get('routes.renderers');

        foreach ($availableRouteTypes as $ext => $handler) {
            if (file_exists($searchFile . $ext)) {
                $routeFile = $searchFile . $ext;
                break;
            }
        }

        if (! file_exists($controlFile)) {
            $controlFile = null;
        }

        return [$routeFile, $controlFile];
    }
}
