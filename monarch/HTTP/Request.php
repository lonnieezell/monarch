<?php

declare(strict_types=1);

namespace Monarch\HTTP;

/**
 * The HTTP Request class captures the details of the current request
 * and provides a simple interface for working with it. This allows
 * for a nicer working interface and easier testing.
 */
class Request
{
    public readonly string $uri;
    public readonly string $method;
    public readonly string $scheme;
    public readonly string $host;
    public readonly int $port;
    public readonly string $path;
    public readonly array $query;
    public readonly string $body;
    public readonly array $headers;
    public readonly array $middleware;

    private static ?Request $instance = null;

    /**
     * Creates a new Request object from the current global state.
     *
     * Example:
     *  $request = Request::createFromGlobals();
     */
    public static function createFromGlobals(): static
    {
        $request = new static();

        $request->uri = $_SERVER['REQUEST_URI'];
        $request->method = strtoupper((string) $_SERVER['REQUEST_METHOD']);
        $request->query = $_GET;

        if ($request->method === 'POST' && ! empty($_POST)) {
            $request->body = $_POST;
        }

        $uriParts = parse_url((string) $request->uri);
        $request->scheme = $uriParts['scheme'] ?? 'http';
        $request->host = $uriParts['host'] ?? '';
        $request->port = $uriParts['port'] ?? 80;
        $request->path = trim($uriParts['path'], '/');
        unset($uriParts);

        $request->body = file_get_contents('php://input');

        // Headers
        $headers = [
            'Content-Type' => new Header('Content-Type', $_SERVER['CONTENT_TYPE'] ?? 'text/html'),
        ];
        foreach (array_keys($_SERVER) as $key) {
            if (sscanf($key, 'HTTP_%s', $header) === 1) {
                // take SOME_HEADER and turn it into Some-Header
                $header = str_replace('_', ' ', strtolower($header));
                $header = str_replace(' ', '-', ucwords($header));

                $headers[$header] = new Header($header, $_SERVER[$key]);
            }
        }
        $request->headers = $headers;

        self::$instance = $request;

        return self::$instance;
    }

    /**
     * Creates a new request object from an array of data.
     * Any values not provided will be set to their default values.
     *
     * Example:
     *  $request = Request::createFromArray([
     *      'uri' => '/foo/bar',
     *      'method' => 'get',
     *      'scheme' => 'http',
     *      'host' => 'example.com',
     *      'port' => 80,
     *      'path' => '/foo/bar',
     *      'query' => [],
     *      'body' => '',
     *      'headers' => []
     *  ]);
     */
    public static function createFromArray(array $data): static
    {
        $request = new static();

        $request->uri = $data['uri'] ?? '';
        $request->method = strtoupper($data['method'] ?? 'get');
        $request->scheme = $data['scheme'] ?? 'http';
        $request->host = $data['host'] ?? '';
        $request->port = $data['port'] ?? 80;
        $request->path = $data['path'] ?? '';
        $request->query = $data['query'] ?? [];
        $request->body = $data['body'] ?? '';
        $request->headers = $data['headers'] ?? [];

        static::$instance = $request;

        return static::$instance;
    }

    /**
     * Returns the current instance of the Request object.
     */
    public static function instance(): static
    {
        if (static::$instance === null) {
            static::$instance = static::createFromGlobals();
        }

        return static::$instance;
    }

    /**
     * Returns an associative array of the request data.
     *
     * Example:
     *  $data = $request->toArray();
     */
    public function toArray(): array
    {
        return [
            'uri' => $this->uri ?? '',
            'method' => $this->method ?? 'GET',
            'scheme' => $this->scheme ?? 'http',
            'host' => $this->host ?? '',
            'port' => $this->port ?? 80,
            'path' => $this->path ?? '',
            'query' => $this->query ?? [],
            'body' => $this->body ?? '',
            'headers' => $this->headers ?? []
        ];
    }

    /**
     * Returns a boolean value indicating whether the request
     * has the specified header.
     *
     * Example:
     *  if ($request->hasHeader('Content-Type')) {
     *     // Do something...
     * }
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * Returns the value of the specified header, or NULL
     * if the header doesn't exist.
     *
     * Example:
     *  $contentType = $request->header('Content-Type');
     */
    public function header(string $name): string|null
    {
        return $this->headers[$name]?->value ?? null;
    }

    /**
     * Returns an array of all headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Returns a boolean value indicating whether the request
     * was initiated by an htmx request.
     */
    public function isHtmx(): bool
    {
        return $this->header('Hx-Request') === 'true';
    }

    /**
     * Returns the current htmx request URL.
     */
    public function currentHtmxUrl(): string|null
    {
        return $this->header('Hx-Current-Url');
    }

    /**
     * Returns a boolean value indicating whether the request
     * is for history restoration after a miss in the local history cache
     */
    public function isHistoryRestoration(): bool
    {
        return $this->header('Hx-History-Restore-Request') === 'true';
    }

    /**
     * Returns a boolean value indicating whether the request
     * was boosted by htmx.
     */
    public function isBoosted(): bool
    {
        return $this->header('Hx-Boosted') === 'true';
    }

    /**
     * Returns a boolean value indicating whether the request
     * was initiated by an htmx prompt.
     */
    public function prompt(): string|null
    {
        return $this->header('Hx-Prompt');
    }

    /**
     * Returns the target of the htmx request.
     */
    public function target(): string|null
    {
        return $this->header('Hx-Target');
    }

    /**
     * Returns the id of the element that triggered the htmx request.
     */
    public function triggerId(): string|null
    {
        return $this->header('Hx-Trigger');
    }

    /**
     * Returns the name of the element that triggered the htmx request.
     */
    public function triggerName(): string|null
    {
        return $this->header('Hx-Trigger-Name');
    }
}
