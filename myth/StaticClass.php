<?php namespace Myth;

class StaticClass
{
	static public $instance;

	public static function factory()
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Provides a "facade" like capability
	 * to call a class statically but get an instance in return.
	 *
	 * @param       $method
	 * @param mixed ...$args
	 *
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		return static::factory()->{$method}(...$args);
	}
}
