<?php

namespace Monarch;

/**
 * @see https://dev.to/odopeter/mastering-the-javascript-console-log-method-your-ultimate-guide-to-error-free-debugging-2j00
 * @see https://developer.mozilla.org/en-US/docs/Web/API/console
 * @see https://bugfender.com/blog/javascript-console-log/
 */
class Debug
{
    public static $instance;

    private array $logs = [];
    private array $styles = [
        'info' => 'background: #2488cb; color: white; padding: 5px;',
        'error' => 'background: #cE0637; color: white; padding: 5px;',
        'warn' => 'background: #D35400; color: white; padding: 5px;',
        'log' => 'background: #1E8449; color: white; padding: 5px;',
        'table' => 'background: #2488cb; color: white; padding: 5px;',
        'bold' => 'font-weight: bold;',
    ];

    public static function instance(): Debug
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Collects the given data and stores it so it can be
     * logged to the browser console when the page is rendered.
     */
    public function add(string $type, mixed $value, ?string $header=null): void
    {
        // Should we capture the log?
        if (config('debug.capture') === true) {
            $this->logs[] = [
                'value' => $value,
                'type' => $type,
                'header' => $header,
            ];
        }

        // Or output it now?
        $this->renderLog($type, [
            'value' => $value,
            'header' => $header,
        ]);
    }

    public function log(mixed $value, ?string $header=null): void
    {
        $this->add('log', $value, $header);
    }

    public function info(mixed $value, ?string $header=null): void
    {
        $this->add('info', $value, $header);
    }

    public function warn(mixed $value, ?string $header=null): void
    {
        $this->add('warn', $value, $header);
    }

    public function debug(mixed $value, ?string $header=null): void
    {
        $this->add('debug', $value, $header);
    }

    public function error(mixed $value, ?string $header=null): void
    {
        $this->add('error', $value, $header);
    }

    public function table(mixed $value, ?string $header=null): void
    {
        $this->add('table', $value, $header);
    }

    public function group(mixed $value): void
    {
        $this->add('group', $value);
    }

    public function groupEnd(): void
    {
        $this->add('groupEnd', null);
    }

    /**
     * Outputs the stored logs to the browser console
     * via a <script> tag.
     */
    public function reportLogs(): string
    {
        $script = '<script>';

        foreach ($this->logs as $log) {
            $script .= $this->buildLog($log['type'], $log) ."\n";
        }
        $script .= '</script>';

        return $script;
    }

    /**
     * Builds a log
     */
    private function buildLog(string $type, mixed $info): string
    {
        if ($type === 'group') {
            return "console.group('". $info['value'] ."');";
        }

        if ($type === 'groupEnd') {
            return "console.groupEnd();";
        }

        $baseStyle = isset($this->styles[$type])
            ? "'{$this->styles[$type]}'"
            : '';
        $headerStyle = isset($this->styles[$type])
            ? "'{$this->styles[$type]} {$this->styles['bold']}'"
            : "'{$this->styles['bold']}'";

        $parts = [];
        $args = [];
        $value = $info['value'];

        // Header
        if (isset($info['header']) && $info['header'] != null) {
            $parts[] = '%c['. $info['header'] . ']';
            $args[] = $headerStyle;
        }

        // Tables
        if ($type === 'table') {
            return "console.log('". implode(" ", $parts) .":', ". implode(", ", $args) ."); "
                ." console.table(". json_encode($value) .");";
        }

        // Value
        if (is_array($value) || is_object($value)) {
            return "console.log('". implode(" ", $parts) .":', ". implode(", ", $args) ."); "
                ." console.log(". json_encode($value) .");";
        }

        if (! empty($baseStyle)) {
            $parts[] = '%c'. $value;
            $args[] = $baseStyle;
        } else {
            $parts[] = $value;
        }

        // Put them together
        return "console.{$type}('". implode(" ", $parts) ."', ". implode(", ", $args) .");";
    }

    /**
     * Renders a log to the browser console
     */
    private function renderLog(string $type, mixed $value): void
    {
        echo "\n<script>". $this->buildLog($type, $value) ."</script>";
    }
}
