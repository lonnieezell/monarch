<?php

declare(strict_types=1);

namespace Monarch\View;

use Monarch\HTTP\Request;
use RuntimeException;

/**
 * Used by the HTML and Markdown renderers to allow for standard HTML-based layouts.
 */
trait HasLayouts
{
    protected function needsLayout(): bool
    {
        if (! property_exists($this, 'request') || ! $this->request instanceof Request) {
            return false;
        }

        // Non-boosted HTMX pages should not have a layout either
        // so it can return the partials that get loaded into the page.
        return ! ($this->request->isHtmx() && ! $this->request->isBoosted());
    }

    /**
     * Renders the content within the layout file.
     * The $routeFile is the full path to the route file, and is used,
     * simply to determine the folder we're in so that we can recursively
     * build out layout from.
     */
    protected function renderInLayout(string $content, string $routeFile): string
    {
        $layoutContent = $this->buildLayout($routeFile);

        if (empty($layoutContent)) {
            return $content;
        }

        // TODO: Allow for multiple named slots in the layout
        return str_replace('<slot></slot>', $content, (string) $layoutContent);
    }

    /**
     * Crafts the layout for the page.
     *
     * @return string
     */
    protected function buildLayout(string $routeFile): string
    {
        // Given the path in $routeFile, we need to find all layout files
        // for each directory in the path, up to ROOTPATH.
        $layoutContent = '';
        $path = dirname($routeFile);
        $pathSegments = explode('/', str_ireplace(ROOTPATH, '', $path));

        $testPath = rtrim(ROOTPATH, '/');

        foreach ($pathSegments as $segment) {
            $testPath .= '/' . $segment;

            if (file_exists($testPath .'/+layout.php')) {
                $content = $this->renderHTMLFile($testPath .'/+layout.php');
                $layoutContent = empty($layoutContent)
                    ? $content
                    : str_replace('<slot></slot>', $content, (string) $layoutContent);
            }
        }

        return $layoutContent;
    }

    /**
     * Renders an HTML/PHP file to a string.
     *
     * Requires the $data and $content properties to be set,
     * which are then available to the view. These properties
     * are already set on the View Renderers in the system.
     *
     * @throws RuntimeException
     */
    protected function renderHTMLFile(string $file): string
    {
        if (! file_exists($file)) {
            throw new RuntimeException("View not found: {$file}");
        }

        // Make data available to the view
        if (is_array($this->data)) {
            $data = $this->data;
        }

        // Make content available to the view
        if (! empty($this->content)) {
            $content = $this->content;
        }

        ob_start();
        include $file;
        return ob_get_clean() ?? '';
    }
}
