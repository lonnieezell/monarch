<?php

declare(strict_types=1);

namespace Monarch\Helpers;

class Numbers
{
    /**
     * Format a number as a human-readable size.
     */
    public static function humanSize(int $bytes, int $dec = 2): string
    {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen((string)$bytes) - 1) / 3);

        if ($factor == 0) {
            $dec = 0;
        }

        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }

    /**
     * Format a number in hours, or minutes, or seconds, or milliseconds,
     * when passed a value in as microseconds, as in the return value of
     * `microtime(true)`.
     */
    public static function humanTime(float $time): string
    {
        if ($time < 1) {
            return round($time * 1000) . 'ms';
        }

        if ($time < 60) {
            return round($time, 2) . 's';
        }

        if ($time < 3600) {
            return round($time / 60, 2) . 'm';
        }

        return round($time / 3600, 2) . 'h';
    }
}
