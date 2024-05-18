<?php

namespace Monarch\Routes;

use Monarch\HTTP\Request;

/**
 * Defines API helper methods for dealing with
 * the success/failure states in an API call.
 *
 * Example usage:
 *  $data = ['name' => 'Monarch'];
 *  return $this->respond($data)->withStatus(API::STATUS_UPDATED, 'User updated');
 */
class API
{
    /** HTTP Status Codes */
    public const STATUS_CREATED = 201;
    public const STATUS_DELETED = 200;
    public const STATUS_UPDATED = 200;
    public const STATUS_NO_CONTENT = 204;
    public const STATUS_INVALID_REQUEST = 400;
    public const STATUS_UNSUPPORTED_RESPONSE_TYPE = 400;
    public const STATUS_INVALID_SCOPE = 400;
    public const STATUS_TEMPORARILY_UNAVAILABLE = 400;
    public const STATUS_INVALID_GRANT = 400;
    public const STATUS_INVALID_CREDENTIALS = 400;
    public const STATUS_INVALID_REFRESH = 400;
    public const STATUS_NO_DATA = 400;
    public const STATUS_INVALID_DATA = 400;
    public const STATUS_ACCESS_DENIED = 401;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_INVALID_CLIENT = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_RESOURCE_NOT_FOUND = 404;
    public const STATUS_NOT_ACCEPTABLE = 406;
    public const STATUS_RESOURCE_EXISTS = 409;
    public const STATUS_CONFLICT = 409;
    public const STATUS_RESOURCE_GONE = 410;
    public const STATUS_PAYLOAD_TOO_LARGE = 413;
    public const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    public const STATUS_TOO_MANY_REQUESTS = 429;
    public const STATUS_SERVER_ERROR = 500;
    public const STATUS_UNSUPPORTED_GRANT_TYPE = 501;
    public const STATUS_NOT_IMPLEMENTED = 501;

    protected int $status = 200;
    protected ?array $body = null;
    protected ?string $message = null;

    /**
     * Gets the response status code.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Returns the message for the response.
     */
    public function message(): ?string
    {
        return $this->message;
    }

    /**
     * Returns the body of the response.
     */
    public function body(): ?array
    {
        return $this->body;
    }

    /**
     * Specifies the routes should use the 'api' middleware group
     * @param string $method
     * @return string|array
     */
    public function middleware(string $method): string|array
    {
        return 'api';
    }

    public function __toString(): string
    {
        return json_encode([
            'status' => $this->status,
            'message' => $this->message,
            'timestamp' => date("Y-m-d H:i:s"),
            'data' => $this->body,
        ]);
    }

    /**
     * Returns the status code for the response.
     *
     * @return static
     */
    protected function withStatus(int $status, ?string $message=null): static
    {
        $this->status = $status;
        $this->message = $message;

        return $this;
    }

    /**
     * Returns the body of the response.
     *
     * @return static
     */
    protected function respond(array $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * A generic failure response.
     *
     * Note: the status code and status message is merged
     * in before it reaches the client.
     *
     * Example:
     *  return $this->fail('Resource not found');
     *  // Output:
     *  // {
     *       "status": 500,
     *       "message": "Unknown Error",
     *       "timestamp": 163234234,
     *       "error": "Resource not found",
     *       "path": "/api/v1/users"
     *     }
     */
    protected function fail(?string $description): static
    {
        $response = [
            'timestamp' => date("Y-m-d H:i:s"),
            'error' => $description ?? 'Unknown Error',
            'path' => Request::instance()->path,
        ];

        if (is_array($this->body) && count($this->body)) {
            $response = array_merge($response, $this->body);
        }

        return $this->respond($response)
            ->withStatus(self::STATUS_SERVER_ERROR);
    }

    /**
     * Used after successfully creating a new resource.
     *
     * Example:
     * return $this->respondCreated($data, 'New user created');
     */
    protected function respondCreated(array $body, ?string $message = null): static
    {
        return $this->respond($body)
            ->withStatus(self::STATUS_CREATED, $message ?? 'Resource created');
    }

    /**
     * Used after successfully deleting a resource.
     */
    protected function respondDeleted(array $body, ?string $message = null): static
    {
        return $this->respond($body)
            ->withStatus(self::STATUS_DELETED, $message ?? 'Resource deleted');
    }

    /**
     * Used after successfully updating a resource.
     */
    protected function respondUpdated(array $body, ?string $message = null): static
    {
        return $this->respond($body)
            ->withStatus(self::STATUS_UPDATED, $message ?? 'Resource updated');
    }

    /**
     * Used after a command has been successfully executed but there is no
     * meaningful reply to send back to the client.
     */
    protected function respondNoContent(?string $message = null): static
    {
        return $this->withStatus(self::STATUS_NO_CONTENT, $message ?? 'No content');
    }

    /**
     * Used when the client is either didn't send authorization information,
     * or had bad authorization credentials. User is encouraged to try again
     * with the proper information.
     */
    protected function failUnauthorized(?string $error = null): static
    {
        return $this->fail($error ?? 'Unauthorized')
            ->withStatus(self::STATUS_UNAUTHORIZED, $message ?? 'Unauthorized');
    }

    /**
     * Used when access is always denied to this resource and no amount
     * of trying again will help.
     */
    protected function failForbidden(?string $error = null): static
    {
        return $this->fail($error ?? 'Forbidden')
            ->withStatus(self::STATUS_FORBIDDEN, $message ?? 'Forbidden');
    }

    /**
     * Used when the resource requested is not found.
     */
    protected function failNotFound(?string $error = null): static
    {
        return $this->fail($error ?? 'Resource not found')
            ->withStatus(self::STATUS_RESOURCE_NOT_FOUND, $message ?? 'Resource not found');
    }

    /**
     * Used when the data provided by the client cannot be validated.
     */
    protected function failValidationError(string $error = 'Bad Request'): static
    {
        return $this->fail($error)
            ->withStatus(self::STATUS_INVALID_REQUEST, $message ?? 'Bad Request');
    }

    /**
     * Used when the data provided by the client cannot be validated on one or more fields.
     */
    protected function failValidationErrors(array $errors): static
    {
        return $this->respond([
            'error' => $errors
        ])
            ->fail('Validation failed')
            ->withStatus(self::STATUS_INVALID_DATA, 'Validation failed');
    }

    /**
     * Used when trying to create a new resource and it already exists.
     */
    protected function failResourceExists(string $description = 'Conflict'): static
    {
        return $this->fail($description)
            ->withStatus(self::STATUS_RESOURCE_EXISTS, $message ?? 'Conflict');
    }

    /**
     * Use when a resource was previously deleted. This is different than
     * Not Found, because here we know the data previously existed, but is now gone,
     * where Not Found means we simply cannot find any information about it.
     */
    protected function failResourceGone(string $description = 'Gone'): static
    {
        return $this->fail($description)
            ->withStatus(self::STATUS_RESOURCE_GONE, $message ?? 'Gone');
    }

    /**
     * Used when the user has made too many requests for the resource recently.
     */
    protected function failTooManyRequests(string $description = 'Too Many Requests'): static
    {
        return $this->fail($description)
            ->withStatus(self::STATUS_TOO_MANY_REQUESTS, $message ?? 'Too Many Requests');
    }
}
