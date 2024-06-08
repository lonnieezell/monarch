<?php

$portOffset = 0;
$options = getopt(null, ['php:', 'host:', 'port:']);

function serve(int $portOffset): void
{
    global $options;

    $php  = escapeshellarg($options['php'] ?? PHP_BINARY);
    $host = $options['host'] ?? 'localhost';
    $port = ($options['port'] ?? 3000) + $portOffset;

    $docRoot = realpath('./public/');
    $version = phpversion();

    print("\033[0;32m Serving on port {$port} with PHP {$version}\n\033[0;39m");

    // Call PHP's built-in webserver, making sure to set our
    // base path to the public folder, and to use the rewrite file
    // to ensure our environment is set, and it simulates basic mod_rewrite.
    passthru("{$php} -S {$host}:{$port} -t {$docRoot}", $status);

    // If the server failed to start, attempt to serve on the next port.
    if ($status && $portOffset < 10) {
        $portOffset++;
        serve($portOffset);
    }
}

serve(0);
