<?php

namespace Monarch;

use RedBeanPHP\R;

/**
 * Class Database
 *
 * Provides simple wrapper around the ReadBean ORM,
 * meant primarily to make connecting to it and
 * accessing it a touch simpler than RedBean already makes it.
 *
 * @package Myth
 */
class Database
{
    public static $instance;

    public static function factory()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns the connection to a
     */
    public function connect(string $group = 'default')
    {
        $driver = config("database.{$group}.driver");

        if ($driver == 'sqlite') {
            R::setup("sqlite:". config("database.{$group}.database"));
            return $this;
        }

        $host = config("database.{$group}.host");
        $port = config("database.{$group}.port");
        $database = config("database.{$group}.database");

        $dsn = "{$driver}:host={$host};port={$port};dbname={$database}";

        R::setup($dsn, config("database.{$group}.user"), config("database.{$group}.password"));

        return $this;
    }

    /**
     * Magic to call the corresponding Redbean command statically
     * but still allow us to chain commands together.
     *
     * @param        $args
     * @return $this
     */
    public function __call(string $method, $args)
    {
        R::{$method}(...$args);

        return $this;
    }
}
