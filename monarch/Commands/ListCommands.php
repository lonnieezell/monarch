<?php

use Monarch\Console\Command;

class ListCommands extends Command
{
    protected string $signature = 'list-commands';
    protected string $description = 'Lists all commands found';
    protected string $group = 'General';
    protected array $commands = [];

    /**
     * Run the command. This is the primary entry-point to
     * the command execution.
     *
     * @return int The exit code for this command.
     */
    public function run(): void
    {
        $this->header('Available Commands:');
        $this->newline();

        // Organize the commands alphabetically, within their groups
        $sortedCommands = [];
        foreach ($this->commands as $command) {
            if (! isset($sortedCommands[$command->group()])) {
                $sortedCommands[$command->group()] = [];
            }

            $sortedCommands[$command->group()][] = $command;
        }

        ksort($sortedCommands);
        $lastGroup = null;

        foreach ($sortedCommands as $group => $rows) {
            if ($lastGroup !== $group) {
                $this->line("{$group}:");
                $lastGroup = $group;
            }

            if (! count($rows)) {
                continue;
            }

            foreach ($rows as $command) {
                $this->line("   {$command->name()} - {$command->description()}", 'success');
            }
        }
    }

    /**
     * Sets the commands to list.
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }
}
