<img src="./_docs/assets/logo.png" style="height: 100px; float: left; margin-right: 2rem;">

# Monarch Web Framework

<div style="clear: both;">

Monarch is an opinionated framework for building web applications. It is designed to be simple, fast, and easy to use. While it allows you to create fully modern web applications, it strips things back to a delicate balance of simplicity and power.

## Philosophy

I believe that web development doesn't need to be overly complex. While there is definitely a place and a need for heavy front-end frameworks, for many projects they are overkill that add unnecessary complexity and mental overhead.

I believe that browsers have come a long way and today's HTML, CSS, and Javascript are more than capable of building powerful web applications without layers and layers of abstractions.

I believe that you can still create modern sites without needing lots of build tools. CSS has enough tools built in now that you don't require a preprocessor. A little bit of Javascript goes a long way. You don't need large frameworks to build reactive components. Web components are available in all modern browsers.

I believe simplicity can be educational. By keeping the abstractions to just what is needed, you can learn the underlying technologies better. You don't need to learn a framework. You need to learn the web. The abstractions make it easy to forget the language you are working in, and never include every feature.

I believe building the web can be fun.

## Features

**Note:** This is a work in progress. Most of the features are not fully implemented yet. Some might not have been started. Consider this a wishlist and a roadmap.

-   **File-Based Routing**: Routes are defined by the file structure and names.
-   **Cascading Layout System**: Layouts can be nested and cascaded, defined alongside the routes they pertain to.
-   **4 Core Route Types**: HTML, Markdown, API, and HTML fragments.
-   **Integrated HTMX**: [HTMX](https://htmx.org/) is included and built right into the routing system.
-   **SQL-Base Database Builder**: SQL is a first-class citizen. It doesn't need to be abstracted away.
-   **Web Components**: Web components are used for building reactive components.
-   ???? Got ideas? Let me know.
-

## Installation

You can install Monarch via Composer:

```bash
composer create-project monarchphp/monarch my-project
```

## Development Server

Monarch comes with a built-in development server. You can start it by running:

```bash
php ./serve.php

# Will output something like:
 Serving on port 3000 with PHP 8.2.23
 [Tue Nov 12 23:59:56 2024] PHP 8.2.23 Development Server (http://localhost:3000) started
```


## Documentation

See the <a href="https://lonnieezell.github.io/monarch-framework/" target="_blank">documentation</a> for more details and usage guides.

## License

Monarch is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
