<?php

namespace Monarch\HTTP;

use Monarch\HTTP\Cookie;
use Monarch\HTTP\Request;

class Response
{
    private static Response $instance;

    private int $status = 200;
    private string|array $body = '';
    private array $headers = [];
    private array $cookies = [];
    private array $swaps = [];

    /**
     * Creates a new Response instance from a Request instance.
     *
     * @TODO This needs to grab relevant info from the request.
     */
    public static function createFromRequest(Request $request): Response
    {
        $response = new static();
        $response->status = 200;

        self::$instance = $response;

        return self::$instance;
    }

    /**
     * Returns the singleton instance of the Response class.
     *
     * @return Response
     */
    public static function instance(): Response
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
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
     * Replaces a header in the response.
     */
    public function replaceHeader(Header $value): static
    {
        $this->forgetHeader($value->name);

        return $this->withHeader($value);
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
        $this->forgetCookie($value->name);

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
     * Adds an OOB swap to the response.
     * The $id is any unique identifier for the swap, often the id of the tag.
     * The $value is the value to swap in.
     *
     * @see https://htmx.org/docs/#oob_swaps
     */
    public function withSwap(string $id, string $value): static
    {
        $this->swaps[$id] = $value;

        return $this;
    }

    /**
     * Removes a swap from the response, if it exists.
     */
    public function forgetSwap(string $id): static
    {
        unset($this->swaps[$id]);

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

        $body = $this->addSwaps();

        // echo the body out
        return $body;
    }

    /**
     * Adds the OOB swaps to the response body.
     * If the body is an array, it will be ignored.
     * If the closing </body> tag is found within the body,
     * the swaps will be added before it.
     */
    private function addSwaps(): string|array
    {
        if (is_array($this->body) || empty($this->swaps)) {
            return $this->body;
        }

        $body = $this->body;
        $swaps = implode("\n", $this->swaps);

        if (strpos($body, '</body>') !== false) {
            $body = str_replace('</body>', "\n". $swaps.'</body>', $body);
        } else {
            $body .= "\n". $swaps;
        }

        return $body;
    }
}
