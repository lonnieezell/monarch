<?php

declare(strict_types=1);

namespace Monarch\Debug;

use Monarch\Concerns\IsSingleton;
use Monarch\Helpers\Numbers;
use Monarch\HTTP\Request;

/**
 * Handles the collection and display of system information,
 * database queries, etc.
 *
 * All display happens to the browser console.
 */
class Info
{
    use IsSingleton;

    private array $data = [];

    public function report(): string
    {
        $output = '<script>';

        $output .= $this->displayBasicStats();
        $output .= $this->displayHtmxStats();
        $output .= $this->displayRequest();

        $output .= '</script>';

        return $output;
    }

    /**
     * Adds a new piece of data to the collection.
     *
     * Example:
     *  Info::instance()->add('database', $query);
     */
    public function add(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function displayBasicStats(): string
    {
        $stats = [];

        if (defined('START_TIME')) {
            $elapsed = microtime(true) - START_TIME;
            $stats['elapsed time'] = Numbers::humanTime($elapsed);
        }

        // Memory usage
        $stats['peak memory'] = Numbers::humanSize(memory_get_peak_usage());

        // HTMX
        $stats['is htmx request'] = Request::instance()->isHtmx();

        $stats = array_merge($stats, $this->data);

        return $this->renderSection(group: 'Monarch: Basic Stats', data: $stats, collapse: false);
    }

    public function displayHtmxStats(): string
    {
        $stats = [
            'current url' => Request::instance()->currentHtmxUrl(),
            'is boosted' => Request::instance()->isBoosted(),
            'is prompt' => Request::instance()->prompt(),
            'target' => Request::instance()->target(),
            'trigger id' => Request::instance()->triggerId(),
            'trigger name' => Request::instance()->triggerName(),
            'is history restoration' => Request::instance()->isHistoryRestoration(),
        ];

        return $this->renderSection(group: 'Monarch: HTMX', data: $stats);
    }

    public function displayRequest(): string
    {
        $headers = [];

        foreach(Request::instance()->headers() as $header) {
            $headers[$header->name] = $header->value;
        }

        return $this->renderSection(group: 'Monarch: Request Headers', data: $headers);
    }

    private function renderSection(string $group, array $data, bool $collapse = true): string
    {
        $output = 'console.group' . ($collapse ? 'Collapsed' : '') ."('{$group}');";
        $output .= 'console.table(' . json_encode($data) . ');';
        $output .= 'console.groupEnd();';

        return $output;
    }
}
