<?php

namespace Monarch\Database;

use Monarch\Database\Connection;

interface ExtensionInterface
{
    /**
     * Extend the connection with additional functionality.
     */
    public static function extend(Connection $connection): void;
}
