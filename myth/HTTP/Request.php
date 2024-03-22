<?php

namespace Myth\HTTP;

use phpDocumentor\Reflection\Types\Null_;

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
        $request->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $request->query = $_GET;

        $uriParts = parse_url($request->uri);
        $request->scheme = $uriParts['scheme'] ?? 'http';
        $request->host = $uriParts['host'] ?? '';
        $request->port = $uriParts['port'] ?? 80;
        $request->path = trim($uriParts['path'], '/');

        $request->body = file_get_contents('php://input');
        $request->headers = function_exists('getallheaders')
            ? \getallheaders()
            : [];

        return $request;
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

        return $request;
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
        return $this->headers[$name] ?? null;
    }

    /**
     * Returns a boolean value indicating whether the request
     * was initiated by an htmx request.
     */
    public function isHtmx(): bool
    {
        return $this->header('HX-Request') === 'true';
    }

    /**
     * Returns a boolean value indicating whether the request
     * was boosted by htmx.
     */
    public function isBoosted(): bool
    {
        return $this->header('HX-Boosted') === 'true';
    }

    /**
     * Returns a boolean value indicating whether the request
     * was initiated by an htmx prompt.
     */
    public function prompt(): string|null
    {
        return $this->header('HX-Prompt');
    }

    /**
     * Returns the target of the htmx request.
     */
    public function target(): string|null
    {
        return $this->header('HX-Target');
    }

    /**
     * Returns the id of the element that triggered the htmx request.
     */
    public function triggerId(): string|null
    {
        return $this->header('HX-Trigger');
    }

    /**
     * Returns the name of the element that triggered the htmx request.
     */
    public function triggerName(): string|null
    {
        return $this->header('HX-Trigger-Name');
    }
}
