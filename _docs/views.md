# Viewing Routes

While the routes documentation provides a high-level overview of how routes work in Myth:work, that information is more geared towards how the routes are found. This document details how the views are combined and displayed to the user.

## Layouts

Layouts are special files that define the primary HTML structure of a page that is displayed to the user. They are defined with the file name `+layout.php`. They must include the `<slot></slot>` tag pair to indicate where the content of the page should be inserted into the layout.

.. note: The page-specific content is generated prior to the layout file being loaded.

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Myth:work</title>
    </head>
    <body>
        <slot></slot>
    </body>
</html>
```

### Nested Layouts

If a subdirectory contains also contains a `+layout.php` file, it will be inserted into the parent layout file in the main slot. There is no maximum depth to the nesting of the layout files. However, all layouts must have their own `<slot></slot>` tag pair otherwise the content, or any child layouts, will not be displayed.

A root-level layout file might look like this:

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Myth:work</title>
    </head>
    <body>
        <h1>First Layout</h1>
        <slot></slot>
    </body>
</html>
```

In a subdirectory, this layout file also exists.

```html
<h2>Second Layout</h2>
<slot></slot>
```

This would result in the following HTML structure:

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Myth:work</title>
    </head>
    <body>
        <h1>First Layout</h1>
        <h2>Second Layout</h2>
        <slot></slot>
    </body>
</html>
```

The route file would then be inserted in the second layout's slot.

## Route Types

Myth:work supports the following route types:

-   Web pages
-   Web page fragments
-   Rest API endpoints
-   Markdown files displayed as web pages.

Route types are defined within the `app/config/routes.php` file. The route type is determined based on the routes's file extension.

## Routes and Layouts

Layout files define the look and basic structure of the page. As such, they are necessary when doing a full page load. However, when loading an HTML fragment, such as a Markdown file or HTML intended to fit into the existing content, the layout file is not needed.

When a route is loaded, the system will check and see if the request was made from HTMX. If it was, and it wasn't the result of a boosted link (which wants the full page) then the layout file is not used and the content is returned directly to the browser. If the request was either not made from HTMX or was from a boosted link, then the layout file is used to wrap the content.

Rest API routes will never use the layouts.
