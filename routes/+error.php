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
    <div class="main container">
        <header class="px-4 py-5 my-5">
            <h1 class="display-5 fw-bold text-body-emphasis">
                <?= $code >= 100 ? $code : '' ?>
                <?= $type ?>
            </h1>
            <div class="mb-4">
                <p class="lead"><?= $message ?></p>
            </div>
        </header>

        <section class="bg-secondary-subtle py-5">
            <div class="container">
                <h2>Stack Trace</h2>
                <pre><?= $trace ?></pre>
            </div>
        </section>

        <footer>
            <p class="text-center py-5">Monarch is an experiment by <a href="https://github.com/lonnieezell" target="_blank">Lonnie Ezell</a></p>
        </footer>
    </div>


    <script src="js/app.js"></script>
</body>

</html>
