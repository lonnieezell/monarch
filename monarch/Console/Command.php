<?php

namespace Monarch\Console;

use RuntimeException;

/**
 * Base class for console commands.
 */
abstract class Command
{
    protected string $name;
    protected string $description;
    protected string $group;
    private Output $output;

    public function __construct()
    {
        $this->output = new Output('default');
    }

    /**
     * Run the command. This is the primary entry-point to
     * the command execution.
     *
     * @return int The exit code for this command.
     */
    abstract public function run(array $arguments): void;

    /**
     * Returns the name of the command.
     */
    public function name(): string
    {
        return $this->name ?? '';
    }

    /**
     * Returnst the command's description.
     */
    public function description(): string
    {
        return $this->description ?? '';
    }

    /**
     * Returns the group this command belongs to.
     */
    public function group(): string
    {
        return $this->group ?? '';
    }

    /**
     * Magic method to allow for calling methods on the output object.
     *
     * @throws RuntimeException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->output, $name)) {
            return $this->output->$name(...$arguments);
        }

        throw new \RuntimeException("Method {$name} does not exist.");
    }
}
