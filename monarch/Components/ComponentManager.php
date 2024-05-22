<?php

namespace Monarch\Components;

use Exception;
use Monarch\Helpers\Files;

/**
 * The ComponentManager class is responsible for managing components
 */
class ComponentManager
{
    protected $components = [];
    private static self $instance;
    private $discovered = false;
    protected array $componentDirectories = [];

    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registers one or more component directories to search for components in.
     */
    public function forComponentDirectories(string|array $directories): self
    {
        if (is_string($directories)) {
            $directories = [$directories];
        }

        $this->componentDirectories = $directories;

        return $this;
    }

    /**
     * Locates all components and saves their location in the components array.
     * They won't actually be instantiated until they are rendered, to ensure
     * we only use the memory that we need.
     */
    public function discover(): void
    {
        if ($this->discovered) {
            return;
        }

        if (empty($this->componentDirectories)) {
            throw new Exception('No component directories have been set.');
        }

        foreach ($this->componentDirectories as $directory) {
            foreach (Files::in($directory) as $file) {
                $componentName = str_replace($directory . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $componentName = str_replace('.php', '', $componentName);
                $componentName = str_replace(DIRECTORY_SEPARATOR, '.', $componentName);

                // Don't register the '.control' files
                if (substr($componentName, -8) === '.control') {
                    continue;
                }

                $this->components[$componentName] = $file->getPathname();
            }
        }

        $this->discovered = true;
    }

    /**
     * Renders out a single component.
     *
     * - $tagName is the name of the component to render.
     * - $rawAttributes is an array of attributes to pass to the component.
     * - $content is the content to pass to the component to be slotted in.
     *
     * @throws Exception
     */
    public function render(string $tagName, array $rawAttributes = [], string $content = '')
    {
        if (!isset($this->components[$tagName])) {
            throw new Exception("Component $tagName not registered.");
        }

        // Check for a control file for this component
        $controlFile = $this->components[$tagName] . '.control';
        $control = file_exists($controlFile) ? include $controlFile : null;

        if ($control) {
            $control->withRouteParams($content, $rawAttributes);
            return $control->render();
        }

        // Otherwise, render the component view directly

        // Make the attributes available to the component
        $attributes = new Attributes($rawAttributes);

        ob_start();
        include $this->components[$tagName];
        $component = ob_get_clean();

        return $this->parseSlots($component, $content);
    }

    /**
     * Handles the parsing of slots in a component.
     * Slots are defined in the component view file using the <x-slot></x-slot> tag.
     * The content passed to the component is slotted in where the <x-slot> tag is.
     * If no content is passed, the default content in the slot is used.
     * Slots can also be named and passed to the component using the <x-slot name="slotName"></x-slot> tag.
     */
    public function parseSlots(string $component, string $viewContent): string
    {
        $pattern = '/([\t\s]*?)<x-slot(?:\s+name="([^"]+)")?>(.*?)<\/x-slot>/s';

        // Loop over each slot in the component and replace it with the content from the view.
        return preg_replace_callback($pattern, function ($matches) use ($viewContent, $component) {
            $slotName = $matches[2] ?? null;
            $defaultSlotContent = $matches[3];

            // If there's no slot name, then replace the default slot.
            if (empty($slotName) && strpos($component, '<x-slot>') !== false) {
                // Remove any other named slots from the component and replace with the view content.
                $viewContent = preg_replace('/[\n\r\t\s]*?<x-slot(.*?)<\/x-slot>/s', '', $viewContent);
                return $matches[1] . $viewContent;
            }

            // Get the content for the named slot from $viewContent if it exists.
            $slotContent = preg_match('/([\t\s]*?)<x-slot name=[\'"]'. $slotName .'[\'"]>(.*?)<\/x-slot>/s', $viewContent, $slotMatches) ? $slotMatches[1] . $slotMatches[2] : null;

            // Replace the slot in the component with the content.
            return $slotContent ?: $defaultSlotContent;
        }, $component);
    }
}
