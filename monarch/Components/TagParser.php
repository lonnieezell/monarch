<?php

namespace Monarch\Components;

use Exception;
use Monarch\Components\ComponentManager;

/**
 * The TagParser class is responsible for parsing custom tags in HTML
 * and replacing them with the output of the corresponding component.
 *
 * Custom tags are defined as <x-tagname>...</x-tagname>
 *
 * The TagParser class uses the ComponentManager to render the components.
 */
class TagParser
{
    private static self $instance;
    private ComponentManager $componentManager;

    private function __construct(ComponentManager $componentManager)
    {
        $this->componentManager = $componentManager;
    }

    /**
     * Returns a singleton instance of the TagParser class.
     * If no ComponentManager is provided, the default instance is used.
     */
    public static function instance(?ComponentManager $componentManager = null): self
    {
        if (!isset($componentManager)) {
            $componentManager = ComponentManager::instance();
        }

        if (!isset(self::$instance)) {
            self::$instance = new self($componentManager);
        }

        return self::$instance;
    }

    /**
     * Parse the HTML and replace custom tags
     * with the output of the corresponding component.
     */
    public function parse(string $html): string
    {
        // TODO: Compare with using a DOM parser
        $pattern = '/<x-(\w+)([^>]*)>(.*?)<\/x-\1>/s';
        return preg_replace_callback($pattern, [$this, 'replaceTag'], $html);
    }

    /**
     * Replace a custom tag with the output of the corresponding component.
     */
    private function replaceTag($matches): string
    {
        $tagName = $matches[1];
        $attributesString = $matches[2];
        $content = $matches[3];

        $attributes = $this->parseAttributes($attributesString);

        $html = $this->componentManager->render($tagName, $attributes, $content);
        return $html;
    }

    /**
     * Parse the attributes string into an associative array.
     */
    private function parseAttributes(string $attributesString): array
    {
        $attributes = [];
        $pattern = '/(\w+)=["\']([^"\']+)["\']/';
        preg_match_all($pattern, $attributesString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $attributes[$match[1]] = $match[2];
        }

        return $attributes;
    }
}
