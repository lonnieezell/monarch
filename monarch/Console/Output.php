<?php

namespace Monarch\Console;

use RuntimeException;

class Output
{
    public function __construct(private string $theme)
    {
    }

    /**
     * Writes a newline to the console.
     */
    public function newline(): void
    {
        echo "\n";
    }

    /**
     * Writes raw content to the console.
     */
    public function raw(string $content): void
    {
        echo $content;
    }

    /**
     * Writes a message to the console.
     *
     * @throws RuntimeException
     */
    public function line(string $message, string $style = 'default'): void
    {
        echo $this->format($message ."\n", $style);
    }

    /**
     * Writes a header message to the console.
     *
     * @throws RuntimeException
     */
    public function header(string $message): void
    {
        $this->breathe($message, 'header');
    }

    /**
     * Writes an error message to the console.
     *
     * @throws RuntimeException
     */
    public function error(string $message, bool $alt = false): void
    {
        $style = $alt ? 'error_alt' : 'error';
        $this->breathe($message, $style);
    }

    /**
     * Writes a success message to the console.
     *
     * @throws RuntimeException
     */
    public function success(string $message, bool $alt = false): void
    {
        $style = $alt ? 'success_alt' : 'success';
        $this->breathe($message, $style);
    }

    /**
     * Writes an info message to the console.
     *
     * @throws RuntimeException
     */
    public function info(string $message, bool $alt = false): void
    {
        $style = $alt ? 'info_alt' : 'info';
        $this->breathe($message, $style);
    }

    /**
     * Writes a message to the console with a newline before and after.
     *
     * @throws RuntimeException
     */
    public function breathe(string $message, string $style = 'default'): void
    {
        $this->newline();
        echo $this->format($message, $style);
        $this->newline();
    }

    /**
     * Formats the given message with the given style.
     *
     * @throws RuntimeException
     */
    public function format(string $message, string $style = 'default'): string
    {
        $styles = Themes::getTheme($this->theme);

        if (isset($styles[$style])) {
            $style = $styles[$style];
        }

        return sprintf("\e[%s%sm%s\e[0m", implode(';', $style), null, $message);
    }
}
