<?php

namespace Monarch\HTTP;

use Monarch\HTTP\Cookie;
use Monarch\HTTP\Request;

class Response
{
    private int $status;
    private string|array $body;
    private array $headers = [];
    private array $cookies = [];

    /**
     * Creates a new Response instance from a Request instance.
     */
    public static function createFromRequest(Request $request): Response
    {
        $response = new static();

        $response->status = 200;

        return $response;
    }

    /**
     * Returns the response status code.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Sets the response status code.
     */
    public function withStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the response body.
     */
    public function body(): string|array
    {
        return $this->body ?? '';
    }

    /**
     * Sets the response body.
     */
    public function withBody(string|array $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Returns the response headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Adds a header to the response.
     */
    public function withHeader(Header $value): static
    {
        $this->headers[] = $value;

        return $this;
    }

    /**
     * Removes a header from the response.
     */
    public function forgetHeader(string $name): static
    {
        foreach ($this->headers as $key => $header) {
            if ($header->name === $name) {
                unset($this->headers[$key]);
            }
        }

        return $this;
    }

    /**
     * Returns the response cookies.
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    /**
     * Adds a cookie to the response.
     */
    public function withCookie(Cookie $value): static
    {
        $this->cookies[] = $value;

        return $this;
    }

    /**
     * Removes a cookie from the response.
     */
    public function forgetCookie(string $name): static
    {
        foreach ($this->cookies as $key => $cookie) {
            if ($cookie->name === $name) {
                unset($this->cookies[$key]);
            }
        }

        return $this;
    }

    /**
     * Sends the response to the client.
     */
    public function send(): string|array
    {
        http_response_code($this->status);

        foreach ($this->headers as $header) {
            if (is_array($header->value)) {
                foreach ($header->value as $value) {
                    header($header->name .': '. $value);
                }

                continue;
            }

            header($header->name .': '. $header->value);
        }

        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie->name,
                $cookie->value,
                $cookie->expires,
                $cookie->path,
                $cookie->domain,
                $cookie->secure,
                $cookie->httpOnly
            );
        }

        // echo the body out
        return $this->body;
    }
}
