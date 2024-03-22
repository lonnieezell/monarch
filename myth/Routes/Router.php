<?php

declare(strict_types=1);

namespace Myth\Routes;

use Myth\HTTP\Request;

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
        $routeFile = $this->basePath . $path . '.php';
        $controlFile = $this->basePath . $path . '.control.php';

        if (! file_exists($routeFile)) {
            $routeFile = null;
        }

        if (! file_exists($controlFile)) {
            $controlFile = null;
        }

        return [$routeFile, $controlFile];
    }
}
