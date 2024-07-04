# Viewing Routes

While the routes documentation provides a high-level overview of how routes work in Monarch, that information is more geared towards how the routes are found. This document details how the views are combined and displayed to the user.

## Layouts

Layouts are special files that define the primary HTML structure of a page that is displayed to the user. They are defined with the file name `+layout.php`. They must include the `<slot></slot>` tag pair to indicate where the content of the page should be inserted into the layout.

.. note: The page-specific content is generated prior to the layout file being loaded.

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Monarch</title>
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
        <title>Monarch</title>
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
        <title>Monarch</title>
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

Monarch supports the following route types:

-   Web pages
-   Web page fragments
-   Rest API endpoints
-   Markdown files displayed as web pages.

Route types are defined within the `app/config/routes.php` file. The route type is determined based on the routes's file extension.

## Routes and Layouts

Layout files define the look and basic structure of the page. As such, they are necessary when doing a full page load. However, when loading an HTML fragment, such as a Markdown file or HTML intended to fit into the existing content, the layout file is not needed.

When a route is loaded, the system will check and see if the request was made from HTMX. If it was, and it wasn't the result of a boosted link (which wants the full page) then the layout file is not used and the content is returned directly to the browser. If the request was either not made from HTMX or was from a boosted link, then the layout file is used to wrap the content.

Rest API routes will never use the layouts.

## Meta Tags

Every HTML page can have any number of meta tags in the header. These are used for everything from the title and description of the page, to analytics tags. Monarch provides a way to define these tags in the control file or route file.

```php
use Monarch\View\Meta;

$meta = Meta::instance();

$meta->setTitle('Monarch');
$meta->addMeta([
    'description' => 'Monarch is a simple and flexible PHP framework.',
    'author' => 'John Doe',
    'keywords' => 'PHP, Framework, Monarch',
]);
```

Besides defining meta tags in the control file, the ViewMeta class also provides convenient methods for managing link tags, script, and style tags.

```php
$meta->addLink([
    'rel' => 'canonical',
    'href' => 'https://example.com/page',
]);
$meta->addScript([
    'src' => 'https://example.com/script.js',
    'type' => 'text/javascript',
]);
$meta->addStyle([
    'href' => 'https://example.com/style.css',
    'type' => 'text/css',
]);

$meta->addRawScript('console.log("Hello, World!");');

```

Markdown files can provide meta tags in the front matter of the file.

```yaml
---
---
title: A Markdown Route
description: This is a markdown file automatically rendered and displayed as a web page.
scripts:
  - src: https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js
    integrity: sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz
    crossorigin: anonymous
styles:
  - href: https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
    integrity: sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH
    crossorigin: anonymous
    rel: stylesheet
---
```

### Displaying Meta Tags

Meta tags are displayed in the layout file using the `getMetaTags()` method. The types of tags that can be managed and displayed are: `title`, `meta`, 'links', `scripts`, `rawScripts`, and `styles`.

```php
// <title>Your Page Title</title>
<?= Meta::instance()->output('title') ?>

// <script src="https://foo/bar.js"></script>
<?= Meta::instance()->output('scripts') ?>

// <meta name="description" content="Your page description">
<?= Meta::instance()->output('meta') ?>

// <link rel="canonical" href="https://example.com/page">
<?= Meta::instance()->output('links') ?>

// <style>body { color: red; }</style>
<?= Meta::instance()->output('styles') ?>

// <script>console.log("Hello, World!");</script>
<?= Meta::instance()->output('styles') ?>
```

### viewMeta() Helper Function

Monarch provides a helper function to make it a little nicer to get the ViewMeta instance: `viewMeta()`. This simply returns the Meta instance.

```php
viewMeta()->addMeta(['description', 'Monarch is a simple and flexible PHP framework.']);
viewMeta()->output('meta');
```

## Escaping User Content

When displaying user-generated content, it is important to escape the content to prevent Cross-Site Scripting (XSS) attacks. Monarch provides several helper functions to escape user-contributed content in a context-aware manner. This is a thin wrapper around the excellent [Laminas Escaper](https://docs.laminas.dev/laminas-escaper/) library.

### Escaping HTML

The `escapeHtml()` function escapes general text being output within the HTML body.

```php
<?= escapeHtml('<script>alert("Hello, World!");</script>') ?>
```

### Escaping HTML Attributes

The `escapeHtmlAttr()` function escapes text that is being output as an HTML attribute.

```php
<div data-foo="<?= escapeHtmlAttr('<script>alert("Hello, World!");</script>') ?>"></div>
```

### Escaping JavaScript

The `escapeJs()` function escapes text that is being output within a `<script>` tag.

```php
<script>
    var foo = <?= escapeJs($foo) ?>;
</script>
```

### Escaping CSS

The `escapeCss()` function escapes text that is being output within a `<style>` tag.

```php
<style>
    body {
        color: <?= escapeCss('red') ?>;
    }
</style>
```

### Escaping URLs

The `escapeUrl()` function escapes text that is being output as part of a URL. It is not necessary to escape the entire URL, only the parts that are user-contributed.

```php
<a href="http://example.com/?q=<?= escapeUrl($query) ?>">Link</a>
```
