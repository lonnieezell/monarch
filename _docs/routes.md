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
    public function get(): string|array|void
    {
        echo 'Hello, World!';
    }
}
```

If the method returns a string, it will be made available to the route file as the `$content` variable. If it returns any other data type,
it will be provided to the route file as the `$data` variable.

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
