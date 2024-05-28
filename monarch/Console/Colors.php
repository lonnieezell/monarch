<?php

namespace Monarch\Console;

/**
 * Inspired by MiniCLI
 * @see https://github.com/minicli/minicli
 */
class Colors
{
    public const FG_BLACK = '0;30';
    public const FG_WHITE = '1;37';
    public const FG_RED = '0;31';
    public const FG_GREEN = '0;32';
    public const FG_BLUE = '1;34';
    public const FG_CYAN = '0;36';
    public const FG_MAGENTA = '0;35';
    public const FG_YELLOW = '0;33';

    public const BG_BLACK = '40';
    public const BG_RED = '41';
    public const BG_GREEN = '42';
    public const BG_BLUE = '44';
    public const BG_CYAN = '46';
    public const BG_WHITE = '47';
    public const BG_MAGENTA = '45';
    public const BG_YELLOW = '43';

    public const BOLD = '1';
    public const DIM = '2';
    public const ITALIC = '3';
    public const UNDERLINE = '4';
    public const INVERT = '7';
}
