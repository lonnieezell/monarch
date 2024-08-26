<?php

declare(strict_types=1);

namespace Monarch\Debug;

use Monarch\HTTP\Request;
use Throwable;

/**
 * Provides exception handling and display for the application.
 */
class Net
{
    private \Throwable $exception;

    public function register(bool $enable=false)
    {
        if ($enable) {
            set_exception_handler([$this, 'handleException']);
            set_error_handler([$this, 'handleError']);
        }
    }

    public function handleException(Throwable $e)
    {
        $this->exception = $e;

        $this->displayError(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
    }

    public function handleError(int $errno, string $errstr, string $errfile, int $errline)
    {
        $this->displayError($errstr, $errstr, $errfile, $errline);
    }

    public function displayError(string $title, string $message, string $file, int $line)
    {
        $trace = $this->exception
            ? $this->exception->getTrace()
            : debug_backtrace();

        $headers = Request::instance()->headers();

        include __DIR__ . '/resources/errors.php';
    }

    public function highlightFile(string $file, int $lineNumber, int $lines = 15): string
    {
        if (! file_exists($file)) {
            return '';
        }

        // Set our highlight colors:
        if (function_exists('ini_set')) {
            ini_set('highlight.comment', '#767a7e; font-style: italic');
            ini_set('highlight.default', '#d7d7d7');
            ini_set('highlight.html', '#06B');
            ini_set('highlight.keyword', '#00dddd');
            ini_set('highlight.string', '#eF06a4');
        }

        try {
            $source = file_get_contents($file);
        } catch (Throwable $e) {
            return false;
        }

        $source = str_replace(["\r\n", "\r"], "\n", $source);
        $source = explode("\n", highlight_string($source, true));

        if (PHP_VERSION_ID < 80300) {
            $source = str_replace('<br />', "\n", $source[1]);
            $source = explode("\n", str_replace("\r\n", "\n", $source));
        } else {
            // We have to remove these tags since we're preparing the result
            // ourselves and these tags are added manually at the end.
            $source = str_replace(['<pre><code>', '</code></pre>'], '', $source);
        }

        // Get just the part to show
        $start = max($lineNumber - (int) round($lines / 2), 0);

        // Get just the lines we need to display, while keeping line numbers...
        $source = array_splice($source, $start, $lines, true);

        // Used to format the line number in the source
        $format = '% ' . strlen((string) ($start + $lines)) . 'd';

        $out = '';
        // Because the highlighting may have an uneven number
        // of open and close span tags on one line, we need
        // to ensure we can close them all to get the lines
        // showing correctly.
        $spans = 0;

        foreach ($source as $n => $row) {
            $spans += substr_count($row, '<span') - substr_count($row, '</span');
            $row = str_replace(["\r", "\n"], ['', ''], $row);

            if (($n + $start + 1) === $lineNumber) {
                preg_match_all('#<[^>]+>#', $row, $tags);

                $out .= sprintf(
                    "<span class='line highlight'><span class='number'>{$format}</span> %s\n</span>%s",
                    $n + $start + 1,
                    strip_tags($row),
                    implode('', $tags[0])
                );
            } else {
                $out .= sprintf('<span class="line"><span class="number">' . $format . '</span> %s', $n + $start + 1, $row) . "\n";
                // We're closing only one span tag we added manually line before,
                // so we have to increment $spans count to close this tag later.
                $spans++;
            }
        }

        if ($spans > 0) {
            $out .= str_repeat('</span>', $spans);
        }

        return '<pre><code>' . $out . '</code></pre>';
    }

    private function cleanPath(string $path): string
    {
        return str_replace(ROOTPATH, '', $path);
    }
}
