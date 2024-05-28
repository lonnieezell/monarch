<?php

namespace Monarch\Console;

use Monarch\Console\Colors;

class Themes
{
    protected static $themes = [
        'default' => [
            'default'     => [Colors::FG_WHITE],
            'alt'         => [Colors::FG_BLACK, Colors::BG_WHITE],
            'error'       => [Colors::FG_RED],
            'error_alt'   => [Colors::FG_WHITE, Colors::BG_RED],
            'success'     => [Colors::FG_GREEN],
            'success_alt' => [Colors::FG_WHITE, Colors::BG_GREEN],
            'info'        => [Colors::FG_CYAN],
            'info_alt'    => [Colors::FG_WHITE, Colors::BG_CYAN],
            'bold'        => [Colors::BOLD],
            'dim'         => [Colors::DIM],
            'italic'      => [Colors::ITALIC],
            'underline'   => [Colors::UNDERLINE],
            'invert'      => [Colors::INVERT],
            'header'      => [Colors::FG_YELLOW],
        ],
    ];

    /**
     * Returns the theme for the given style.
     */
    public static function getTheme(string $theme): array
    {
        if (! isset(self::$themes[$theme])) {
            throw new \RuntimeException("Unknown theme: {$theme}");
        }

        return self::$themes[$theme];
    }
}
