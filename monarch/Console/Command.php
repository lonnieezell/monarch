<?php

namespace Monarch\Console;

use RuntimeException;

/**
 * Base class for console commands.
 */
abstract class Command
{
    protected string $signature;
    protected string $description;
    protected string $group;
    protected array $options = [];
    private array $foundOptions = [];
    private array $arguments = [];
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
    abstract public function run(): void;

    /**
     * Returns the name of the command.
     */
    public function name(): string
    {
        $name = $this->signature;

        if (empty($name)) {
            throw new RuntimeException('Command signature is required.');
        }

        return strpos($name, ' ') !== false
            ? substr($name, 0, strpos($name, ' '))
            : $name;
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
     * Given an array of all values from $argv
     * will collect our argument and option values
     * based on the arguments listed in $signature
     * and the matching option values.
     */
    public function parseArguments(array $args)
    {
        $this->parseSignature();
        $this->parseAvailableOptions();

        foreach ($args as $index => $arg) {
            if (strpos($arg, '--') === 0) {
                if ($this->setOption($arg, 'long', $args[$index + 1])) {
                    $index++;
                }
            } elseif (strpos($arg, '-') === 0) {
                if ($this->setOption($arg, 'short', $args[$index + 1])) {
                    $index++;
                }
            } else {
                if (! isset($this->arguments[$index])) {
                    throw new RuntimeException('Invalid argument provided: '. $arg);
                }
                $this->arguments[$index]['value'] = $arg;
            }
        }

        $this->validateArguments();
    }

    /**
     * Returns the value of the given argument.
     */
    protected function argument(string $name)
    {
        foreach ($this->arguments as $argument) {
            if ($argument['name'] == $name) {
                return $argument['value'];
            }
        }

        return null;
    }

    /**
     * Returns the value of the given option,
     * by either its short or long version.
     */
    protected function option(string $name)
    {
        $name = strtolower(ltrim($name, '- '));

        foreach ($this->foundOptions as $option) {
            if ($option['long'] == $name || $option['short'] == $name) {
                return $option['value'];
            }
        }

        return null;
    }

    /**
     * Sets an option in our foundOptions array
     */
    private function setOption(string $name, string $type, $value=null): bool
    {
        $name = ltrim($name, '- ');
        $value = trim($value, '"\' ');

        foreach ($this->foundOptions as $index => $option) {
            if ($option[$type] == $name) {
                $this->foundOptions[$index]['found'] = true;
                $this->foundOptions[$index]['value'] = $this->foundOptions[$index]['requiresValue']
                    ? $value
                    : null;

                return $this->foundOptions[$index]['requiresValue'];
            }
        }

        return false;
    }

    /**
     * Parse each available option to create an array
     * of short and long options that we can compare with.
     * @return void
     */
    private function parseAvailableOptions(): void
    {
        foreach ($this->options as $option => $description) {
            $option = ltrim($option, '-');
            $requiresValue = strpos($option, '=') !== false;

            $short = '';
            $long = $option;

            if (strpos($option, '|') !== false) {
                [$short, $long] = explode('|', $option);
            }

            $this->foundOptions[] = [
                'short' => $short,
                'long' => $long,
                'description' => $description,
                'found' => false,
                'value' => false,
                'requiresValue' => $requiresValue,
            ];
        }
    }

    /**
     * Extracts all arguments from this->signature.
     */
    private function parseSignature(): void
    {
        $parts = explode(' ', $this->signature);

        foreach ($parts as $part) {
            if (strpos($part, '{') === 0) {
                $part = str_replace(['{', '}'], '', $part);
                $optional = strpos($part, '?') !== false;
                $part = str_replace('?', '', $part);

                $this->arguments[] = [
                    'name' => $part,
                    'isOptional' => $optional,
                    'value' => null
                ];
            }
        }
    }

    /**
     * Validates that all required arguments have been provided.
     */
    private function validateArguments()
    {
        foreach ($this->arguments as $index => $argument) {
            if ($argument['isOptional']) {
                continue;
            }

            if (! isset($argument['value']) || $argument['value'] === false) {
                throw new RuntimeException('Missing required argument: '. $argument['name']);
            }
        }
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
