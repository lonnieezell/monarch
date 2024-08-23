<?php

declare(strict_types=1);

namespace Monarch\Components;

use Attribute;

/**
 * Forms the base of all controlled components.
 */
abstract class Component
{
    protected string $viewContent = '';
    protected Attributes $attributes;

    public function withRouteParams(string $viewContent, Attributes $attributes): static
    {
        $this->viewContent = $viewContent;
        $this->attributes = $attributes;

        return $this;
    }

    abstract public function render(): string;

    /**
     * Provides a simple way to render a component's view file.
     */
    public function view(string $view, array $data = []): string
    {
        // make the attributes available to the view
        $attributes = $this->attributes;
        if (count($data)) {
            extract($data);
        }

        ob_start();
        include __DIR__ . "/{$view}.php";
        $component = ob_get_clean();

        return ComponentManager::instance()->parseSlots($component, $content);
    }
}
