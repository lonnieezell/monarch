# Routes

Routing a URI to the code that should execute it is a fundamental part of any web application. In Monarch, routes are defined using a folder-based approach.
Each URI is directly associated with a route file. The route file is a file that determines what happens when a user visits a specific URI.

An example folder structure might look like this:

```
routes/
   home.php
   about.php
   blog/
      index.php
      post[id].php
      archive[year][month].php
```

In this example, the `home.php` file would be responsible for handling the `/` URI. The `about.php` file would handle the `/about` URI.
The `blog/index.php` file would handle the `/blog` URI, and the `blog/post[id].php` file would handle the `/blog/post/123` URI.

## Layouts

Special Layout files can be used to define the structure of a page. To define a master layout file to be used on all web files, create a file within
the `routes` folder called `+layout.php`.

```
routes/
   +layout.php
   home.php
```

The `+layout.php` file defines the HTML structure of the page. It should include the `<?= $content ?>` tag to indicate where the content of the page should be placed.

### Nested Layouts

If a subfolder contains a `+layout.php` file, it will be inserted into the parent layout file. There is not maximum depth to the nesting of the layout files.
All web files at that folder level will use the combined layout files.

## Route Types

By default, Monarch supports the following route types:

-   Web pages
-   Web page fragments
-   Rest API endpoints
-   Markdown files displayed as web pages.

Route types are defined within the `app/config/routes.php` file.

Route types are determine based on the route files' extension. The default route files are:

-   `.php` for web pages
-   `-{name}.php` for fragments
-   `.api.php` for Rest API endpoints
-   `.md` for Markdown files.

## Errors

If a route file ends up throwing an error, the default error page found at `/+error.php` will be displayed to the user.

You can customize the main file to change the look of the error page. You can also create custom error pages within
each of the route folders to customize the error page for that specific route.

## Control Files

Control files are used to define the logic for a route. They are optional, and are only needed if you need to do more than just display a basic page.

Control files are named the same as the route file, but with a `.control.php` extension. For example, the `home.php` route file would use the `home.control.php` control file.

Within each control file, you can define an anonymous class that has methods that match the HTTP verbs you want to handle. For example, to handle a `GET` request, you
would define a `get()` method. To handle a `POST` request, you would define a `post()` method.

```php
<?php

return new class()
{
    public function get(): string
    {
        return 'Hello, World!';
    }
}
```

If the method returns a string, it will be made available to the route file as the `$content` variable. If it returns any other data type, it will be provided to the route file as the `$data` variable.

If you need to provide both the `$content` and `$data` variables, you can return an array with the `content` and `data` keys.

```php
<?php

return new class()
{
    public function get(): array
    {
        return [
            'content' => 'Hello, World!',
            'data' => [1, 2, 3],
        ];
    }
}
```

## Dynamic Routes

Dynamic routes are routes that contain variables within the URI. For example, a route that handles a blog post might look like this:

```
routes/
   blog/
      post[id].php
```

In this example, the `post[id].php` file would handle the `/blog/post/123` URI. The `id` variable would be available within the control file as a parameter.

```php
<?php

return new class()
{
    public function get(int $id): string
    {
        return 'The post ID is: ' . $id;
    }
}
```

You many include as many dynamic variables as you need within the route file. They will be passed to the control file in the order they are defined.

## API Routes

API routes are similar to control files, but they are designed to return JSON data instead of HTML content. API routes are defined by using the `.api.php` extension. The class should extend the `Monarch\API` class, which provides helper methods for responding to API requests and handling errors.

```php
<?php

use Monarch\API;

return new class() extends API
{
    public function get(): array
    {
        return $this->respond([
            'message' => 'Hello, World!',
        ]);
    }
}
```

Like control routes, API routes should have methods that match the HTTP verbs you want to handle. Any data returned must be either null or an array that can be converted to JSON.

### Response Methods

API routes have access to the following response methods:

-   `respond(array $data): array` - Respond with a JSON object.
-   `withStatus(int $status ?string $message=null): self` - Set the status code of the response.

```php
public function get()
{
    return $this->respond([
        'message' => 'Resource created',
    ])->withStatus(200);
}
```

-  `fail(?string $description=null)` - Respond with an error message. If a description is provided, it will be included in the response, otherwise the message will be "Unknown Error". The response also includes the timestamp of the error, the status code, and the URI path that caused the error.

```php
public function get()
{
    $resource = $this->getResource();

    if (! $resource) {
        return $this->fail('Resource not found');
    }
}

// Returns the following JSON:
{
    "error": "Resource not found",
    "message": "Resource not found",
    "timestamp": "2021-10-01 12:00:00",
    "status": 404,
    "path": "/api/resource/123"
}
```

- `respondCreated(array $body, ?string $message = null)` - Respond with a 201 status code and a JSON object. The body of the response is the data provided in the `$body` parameter. If a message is provided, it will be included in the response.

```php
public function post()
{
    $resource = $this->createResource();

    return $this->respondCreated($resource, 'Resource created');
}
```

- `respondDeleted(array $body, ?string $message = null)` - Respond with a 200 status code and a JSON object. The body of the response is the data provided in the `$body` parameter. If a message is provided, it will be included in the response.

```php
public function delete()
{
    $resource = $this->deleteResource();

    return $this->respondDeleted($resource, 'Resource deleted');
}
```

- `respondUpdated(array $body, ?string $message = null)` - Respond with a 200 status code and a JSON object. The body of the response is the data provided in the `$body` parameter. If a message is provided, it will be included in the response.

```php
public function put()
{
    $resource = $this->updateResource();

    return $this->respondUpdated($resource, 'Resource updated');
}
```

- `respondNoContent(?string $message = null)` - Respond with a 204 status code and no content. If a message is provided, it will be included in the response.

```php
public function delete()
{
    $this->deleteResource();

    return $this->respondNoContent('Resource deleted');
}
```

- `failUnauthorized(?string $error = null)` - Respond with a 401 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Unauthorized".

```php
public function get()
{
    if (! $this->isAuthorized()) {
        return $this->failUnauthorized('Unauthorized');
    }
}
```

- `failForbidden(?string $error = null)` - Respond with a 403 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Forbidden".

```php
public function get()
{
    if (! $this->isAuthorized()) {
        return $this->failForbidden('Forbidden');
    }
}
```

- `failNotFound(?string $error = null)` - Respond with a 404 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Not Found".

```php
public function get()
{
    $resource = $this->getResource();

    if (! $resource) {
        return $this->failNotFound('Resource not found');
    }
}
```

- `failValidationError(string $error = 'Bad Request')` - Respond with a 400 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Bad Request". Used when the data provided by the client cannot be validated.

```php
public function post()
{
    $data = $this->getRequestData();

    if (! $this->validateData($data)) {
        return $this->failValidationError('Invalid data');
    }
}
```

- `failValidationErrors(array $errors)` - Respond with a 400 status code and an array of error messages. The error message is a JSON object that contains the errors provided in the `$errors` parameter. Used when the data provided by the client cannot be validated for one or more fields.

```php
public function post()
{
    $errors = $this->validateData($data);

    if ($errors) {
        return $this->failValidationErrors($errors);
    }
}
```

- `failResourceExists(string $description = 'Conflict')` - Respond with a 409 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Conflict". Used when the resource already exists and cannot be created.

```php
public function post()
{
    $resource = $this->getResource();

    if ($resource) {
        return $this->failResourceExists('Resource already exists');
    }
}
```

- `failResourceGone(string $description = 'Gone')` - Respond with a 410 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Gone". Used when the resource has been deleted and cannot be accessed.

```php
public function get()
{
    $resource = $this->getResource();

    if (! $resource) {
        return $this->failResourceGone('Resource has been deleted');
    }
}
```

- `failTooManyRequests(string $description = 'Too Many Requests')` - Respond with a 429 status code and an error message. If an error string is provided, it will be included in the response, otherwise the message will be "Too Many Requests". Used when the client has sent too many requests in a given amount of time.

```php
public function get()
{
    if ($this->isRateLimited()) {
        return $this->failTooManyRequests('Rate limit exceeded');
    }
}
```

### Monarch Routes

When the `DEBUG` constant is set to `true`, Monarch provides a set of routes to provide tools for you to use. These all use the `_/` folder to avoid conflicts with your own routes. You should not use a base folder in your routes folder called `_` otherwise that will override the Monarch routes.
