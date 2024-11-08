<?php

declare(strict_types=1);

namespace Monarch\Routes;

use Monarch\HTTP\Request;
use Monarch\View\Renderer;
use RuntimeException;

/**
 * Given a URL and a base folder, this class will attempt to determine the
 * various files that make up our file-based routing system.
 */
class Router
{
    public readonly array $basePath;
    public readonly string $routeFile;
    public readonly string $controlFile;
    public readonly ?array $routeParams;

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
        $paths = [
            rtrim($path, '/ ') . '/',
        ];

        // Enable Monarch routes in debug mode.
        if (DEBUG) {
            $paths[] = MONARCHPATH;
        }

        $this->basePath = $paths;

        return $this;
    }

    /**
     * Given a request, will attempt to find the route and controller files.
     *
     * Example:
     *  $router = new Router();
     *  $router->setBasePath(ROOTPATH .'routes');
     *  $route = $router->getFilesForRequest($request);
     */
    public function getRouteForRequest(Request $request): Route
    {
        $path = ltrim($request->path ?: 'index', '/');
        $path = strtolower($path);
        $path = str_replace(['/', '.'], DIRECTORY_SEPARATOR, $path);

        $routeFile = '';
        $controlFile = '';

        // Do we have a direct match?
        [$routeFile, $controlFile] = $this->searchForFiles($path .'.*');

        // Handle when directory name matches route and has an index file.
        foreach ($this->basePath as $basePath) {
            if ($routeFile === '' && is_dir($basePath . $path)) {
                [$routeFile, $controlFile] = $this->searchForFiles($path .'/index.*');
            }
        }

        // Handle dynamic routes (i.e. /posts[year][month].php and posts[year][month].control.php)
        if ($routeFile === '') {
            $segments = explode('/', $path);
            $pathRoute = '';

            while (count($segments)) {
                // Build the base path portion (i.e. /path/to/file)
                $pathRoute .= (DIRECTORY_SEPARATOR . array_shift($segments));

                // Add any remaining segments as placeholders
                $searchPath = $pathRoute . array_reduce($segments, function ($carry, $item) {
                    return $carry .'\[*\]';
                });

                [$routeFile, $controlFile] = $this->searchForFiles(ltrim($searchPath, DIRECTORY_SEPARATOR) . '.*');

                if ($routeFile !== '') {
                    break;
                }
            }
        }

        $this->routeFile = $routeFile;
        $this->controlFile = $controlFile;
        $this->routeParams = $this->getRouteParams($path, $searchPath ?? '');

        return new Route(
            routeFile: $this->routeFile,
            controlFile: $this->controlFile,
            params: $this->routeParams,
        );
    }

    /**
     * Determines what to display based on the request,
     * loads the control and route files, and renders the appropriate output.
     *
     * @throws RuntimeException
     */
    public function display(Request $request, Route $route, ?object $control = null): string|array
    {
        // Defaults
        $content = 'index';
        $data = [];

        if ($control && method_exists($control, strtolower($request->method))) {
            $output = $control->{strtolower($request->method)}(...($routeParams ?? []));

            if (is_array($output)) {
                $content = $output['content'] ?? $content;
                $data = $output['data'] ?? $output;
            } elseif (is_string($output)) {
                $content = $output;
            }
        }

        $renderer = Renderer::createWithRequest($request);
        $output = $renderer
            ->withRouteParams(content: $content, data: $data)
            ->render($route->routeFile) ?? '';

        return $output;
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

        foreach($this->basePath as $basePath) {
            $result = glob($basePath . $pattern);

            if ($result === false) {
                continue;
            }

            if (is_array($result) && count($result) > 0) {
                $routeFile = $result[0];

                // If 2 matches, then the control file is the first one
                if (count($result) === 2) {
                    $controlFile = $result[0];
                    $routeFile = $result[1];
                }
            }
        }

        if ($result === false && $routeFile === '') {
            throw new RuntimeException('Error scanning for route files.');
        }

        return [$routeFile, $controlFile];
    }

    /**
     * Given a path and a search path, will attempt to extract any route
     * parameters from the path.
     */
    private function getRouteParams(string $path, string $searchPath): ?array
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
        $routeParts = explode('[', $this->routeFile);

        foreach ($searchParts as $index => $part) {
            if ($part === '*]') {
                $alias = substr($routeParts[$index], 0, strpos($routeParts[$index], ']'));
                $params[$alias] = $pathParts[$index];
            }
        }

        return count($params) === 0 ? null : $params;
    }
}
