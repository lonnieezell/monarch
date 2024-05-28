<?php

namespace Monarch\Console;

use RuntimeException;

/**
 * Manages Console Commands.
 */
class Console
{
    private array $paths = [];
    private array $commands = [];

    /**
     * Registers a new path to look in for console commands.
     */
    public function registerPath(string $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Register a new Command in the system.
     */
    public function registerCommand(Command $command): static
    {
        $this->commands[$command->name()] = $command;

        return $this;
    }

    /**
     * Runs the given command.
     * If no command is specified, will run the 'command:list' command
     * to list all available commands.
     *
     * @throws RuntimeException
     */
    public function run()
    {
        global $argv;

        $this->discoverCommands();

        $commandName = $argv[1] ?? null;
        $arguments = array_slice($argv, 2);

        // Default to List command
        if (! isset($commandName)) {
            $commandName = 'list-commands';
            $arguments = ['commands' => $this->commands];
        }

        if (! isset($this->commands[$commandName])) {
            throw new RuntimeException('Unable to locate desired command.');
        }

        $this->commands[$commandName]->run($arguments);
    }

    /**
     * Return a list of all available commands.
     */
    private function discoverCommands(): void
    {
        foreach ($this->paths as $path) {
            foreach (glob($path . '/*.php') as $filename) {
                // Include the file
                include_once $filename;

                $className = basename($filename, '.php');
                $class = new $className();
                $commandName = $class->name();

                // Store the command name and class instance in the $this->commands array
                $this->commands[$commandName] = $class;
            }
        }
    }
}
