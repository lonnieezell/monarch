<?php

namespace App;

/**
 * Provides a single place to access that common classes in a singleton pattern,
 * boosting performance and reducing memory usage.
 *
 * All methods should be static, and the class should never be instantiated.
 *
 * To take advantage of the singleton pattern, the tool definitions should
 * use the Myth\Singleton library, which will ensure that only one instance
 * of the class is created.
 */
class Tools
{
    public static function something()
    {
        return 'something';
    }
}
