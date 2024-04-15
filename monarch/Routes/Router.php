<?php

declare(strict_types=1);

namespace Monarch\Routes;

use Monarch\Config;
use Monarch\HTTP\Request;
use RuntimeException;
use SplFixedArray;

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

        $routeFile = '';
        $controlFile = '';

        // Do we have a direct match?
        [$routeFile, $controlFile] = $this->searchForFiles($this->basePath . $path .'.*');

        // Handle when directory name matches route and has an index file.
        if ($routeFile === '' && is_dir($this->basePath . $path)) {
            // $patterns[] = $this->basePath . $path .'/index.*';
            [$routeFile, $controlFile] = $this->searchForFiles($this->basePath . $path .'/index.*');
        }

        // Check for files with placeholders in the path
        if ($routeFile === '' && strpos($path, '/') !== false) {
            $segments = explode('/', $path);
            $pathRoute = '';

            while (count($segments)) {
                // Build the base path portion (i.e. /path/to/file)
                $pathRoute .= (DIRECTORY_SEPARATOR . array_shift($segments));

                // Add any remaining segments as placeholders
                $searchPath = $pathRoute . array_reduce($segments, function ($carry, $item) {
                    return $carry .'\[*\]';
                });

                [$routeFile, $controlFile] = $this->searchForFiles($this->basePath . ltrim($searchPath, DIRECTORY_SEPARATOR) . '.*');

                if ($routeFile !== '') {
                    break;
                }
            }
        }

        return [
            $routeFile,
            $controlFile,
            $this->getRouteParams($path, $searchPath ?? '', $routeFile)
        ];
    }

    /**
     * Searches for route and control files based on a pattern.
     *
     * @return array [routeFile, controlFile]
     * @throws RuntimeException
     */
    private function searchForFiles(string $pattern): array
    {
        $routeFile = '';
        $controlFile = '';
        $result = glob($pattern);

        if ($result === false) {
            throw new RuntimeException('Error scanning for route files.');
        }

        if (is_array($result) && count($result) > 0) {
            $routeFile = $result[0];

            // If 2 matches, then the control file is the first one
            if (count($result) === 2) {
                $controlFile = $result[0];
                $routeFile = $result[1];
            }
        }

        return [$routeFile, $controlFile];
    }

    /**
     * Given a path and a search path, will attempt to extract any route
     * parameters from the path.
     */
    private function getRouteParams(string $path, string $searchPath, string $routeFile): ?array
    {
        $params = [];

        if ($searchPath === '') {
            return null;
        }

        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $searchPath = str_replace(DIRECTORY_SEPARATOR, '/', $searchPath);
        $searchPath = str_replace('\\', '', $searchPath);
        $searchPath = str_replace('.*', '', $searchPath);

        $pathParts = explode('/', $path);
        $searchParts = explode('[', $searchPath);
        $routeParts = explode('[', $routeFile);

        foreach ($searchParts as $index => $part) {
            if ($part === '*]') {
                $alias = substr($routeParts[$index], 0, strpos($routeParts[$index], ']'));
                $params[$alias] = $pathParts[$index];
            }
        }

        return count($params) === 0 ? null : $params;
    }
}
