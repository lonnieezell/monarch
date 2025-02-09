<!doctype html>
<html class="no-js" lang="en-US" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= viewMeta()->title() ?></title>

    <?= viewMeta()->output('meta') ?>

    <link href="/css/vendor/bootstrap-5.3.min.css" rel="stylesheet">
    <?= viewMeta()->output('styles') ?>
    <script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
    <?= viewMeta()->output('scripts') ?>
    <?= viewMeta()->output('rawScripts') ?>
</head>

<body>
    <div class="main">
        <x-navbar>
            <ul class="nav">
                <li class="nav-item" hx-boost="true">
                    <a class="nav-link" href="/">Welcome</a>
                </li>
                <li class="nav-item" hx-boost="true">
                    <a class="nav-link" href="/about">About</a>
                </li>
                <li class="nav-item" hx-boost="true">
                    <a class="nav-link" href="/markdown">Markdown</a>
                </li>
                <li class="nav-item" hx-boost="true">
                    <a class="nav-link" href="/api">API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/error">Error</a>
                </li>
            </ul>
        </x-navbar>

        <section class="bg-secondary-subtle py-5">
            <div class="container">
                <slot></slot>
            </div>
        </section>
        <footer>
            <div class="text-center pt-5">
                <img src="/img/logo.png" alt="Monarch logo" class="mx-auto" style="max-width: 100px;">
            </div>
            <p class="text-center pt-2">Monarch is an experiment by <a href="https://github.com/lonnieezell" target="_blank">Lonnie Ezell</a></p>
            <p class="text-center pt-2 pb-5 opacity-50">
                Crafted by a kaleidoscope of digital butterflies.
            </p>
        </footer>
    </div>


    <script src="js/app.js"></script>
</body>

</html>
