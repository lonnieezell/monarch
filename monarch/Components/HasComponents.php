<?php

namespace Monarch\Components;

trait HasComponents
{
    /**
     * Parses the HTML for custom components.
     */
    private function parseComponents(string $html): string
    {
        $components = ComponentManager::instance()
            ->forComponentDirectories(ROOTPATH . 'app/components');
        $components->discover();

        return TagParser::instance($components)
            ->parse($html);
    }
}
