<?php

declare(strict_types=1);

namespace Monarch;

use Monarch\Concerns\IsSingleton;
use RuntimeException;

/**
 * Class View
 *
 * Provides simple templating features
 *
 * @package Myth
 */
class View
{
    use IsSingleton;

    /**
     * The name of the layout to use
     *
     * @var string
     */
    protected $layout;

    /**
     * Holds the slot contents until rendering
     * @var array
     */
    protected $slots = [];

    /**
     * Name of the current slot
     *
     * @var string
     */
    protected $currentSlot;

    /**
     * The base path to look for views in.
     * This is the 'routes' folder by default.
     *
     * @var string
     */
    protected string $basePath;

    public static function factory(?string $basePath=null)
    {
        if (self::$instance === null) {
            self::$instance = new self($basePath);
        }

        return self::$instance;
    }

    public function __construct(?string $basePath)
    {
        $this->basePath = $basePath ?? ROOTPATH .'routes';
    }

    /**
     * Includes the contents of a single view
     * file. Intended to use for displaying the
     * contents of partials within other views.
     *
     * @param string     $view
     */
    public function display(string $file, ?array $data): string
    {
        $path = "{$this->basePath}/{$file}.php";

        if (! file_exists($path)) {
            throw new RuntimeException("File not found: {$path}");
        }

        if (is_array($data)) {
            extract($data);
        }

        ob_start();
        include $path;
        return ob_get_clean() ?? '';
    }

    public function render(array $data = [])
    {
        return $this->display('theme/'. $this->layout, $data);
    }

    /**
     * Sets the layout view to use as our template.
     */
    public function extends(string $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Starts capturing the output to insert
     * into a slot() in a template.
     */
    public function startSlot(string $name)
    {
        $this->currentSlot = $name;

        ob_start();
    }

    /**
     * Stops capturing content for a slot.
     */
    public function endSlot()
    {
        $contents = ob_get_clean();

        if (empty($this->currentSlot)) {
            throw new RuntimeException('Views, no current section.');
        }

        // Ensure an array exists so we can store multiple entries for this.
        if (! array_key_exists($this->currentSlot, $this->slots)) {
            $this->slots[$this->currentSlot] = [];
        }
        $this->slots[$this->currentSlot][] = $contents;

        $this->currentSlot = null;
    }

    /**
     * Creates a placeholder slot within a template files
     * that can have content defined in a different view.
     *
     * @return string
     */
    public function slot(string $name): string
    {
        $output = '';

        if (! isset($this->slots[$name])) {
            return $output;
        }

        foreach ($this->slots[$name] as $key => $contents) {
            $output .= $contents;
            unset($this->slots[$name][$key]);
        }

        return $output;
    }
}
