<?php
viewMeta()->addStyle(["rel" => "stylesheet", "href" => "https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css"]);
viewMeta()->addScript(['src' => 'https://unpkg.com/htmx.org@1.9.12', 'integrity' => 'sha384-ujb1lZYygJmzgSwoxRggbCHcjc0rB2XoQrxeTUQyRjrOnlCoYta87iKBWq3EsdM2', 'crossorigin' => 'anonymous']);
?>
<!doctype html>
<html lang="en-US">
<head>
    <meta name="color-scheme" content="light dark"/>
    <title><?= viewMeta()->title() ?></title>

    <?= viewMeta()->output('meta') ?>

    <?= viewMeta()->output('styles') ?>
    <?= viewMeta()->output('scripts') ?>
    <?= viewMeta()->output('rawScripts') ?>
</head>

<body>
<header>
    <x-navbar>
        <ul>
            <li>
                <a href="/">Welcome</a>
            </li>
            <li>
                <a href="/about">About</a>
            </li>
            <li>
                <a href="/markdown">Markdown</a>
            </li>
            <li>
                <a href="/api">API</a>
            </li>
            <li>
                <a href="/error">Error</a>
            </li>
        </ul>
    </x-navbar>
</header>
<main>
    <section>
        <slot></slot>
    </section>
</main>
<footer>
    <div>
        <img src="/img/logo.png" alt="Monarch logo" style="max-width: 100px;">
    </div>
    <p>Monarch is an experiment by <a href="https://github.com/lonnieezell"
                                      target="_blank">Lonnie Ezell</a></p>
    <p>
        Crafted by a kaleidoscope of digital butterflies in <strong>{elapsed_time}</strong> using
        <strong>{memory_usage}</strong>
        of memory.
    </p>
</footer>

<script src="js/app.js"></script>
</body>

</html>
