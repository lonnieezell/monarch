<!doctype html>
<html class="no-js" lang="en-US" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <meta name="description" content="">

    <link href="/css/vendor/bootstrap-5.3.min.css" rel="stylesheet">
    <script src="/js/vendor/htmx-1.9.11.min.js"></script>
</head>

<body>
    <div class="main">
        <nav hx-boost="true">
            <ul class="nav justify-content-center bg-dark py-3">
                <li class="nav-item">
                    <a class="nav-link" href="/">Welcome</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/markdown">Markdown</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/api">API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/error">Error</a>
                </li>
            </ul>
        </nav>
        <section class="bg-secondary-subtle py-5">
            <div class="container">
                <slot></slot>
            </div>
        </section>
        <footer>
            <p class="text-center py-5">Myth:work is an experiment by <a href="https://github.com/lonnieezell" target="_blank">Lonnie Ezell</a></p>
        </footer>
    </div>


    <script src="js/app.js"></script>
</body>

</html>
