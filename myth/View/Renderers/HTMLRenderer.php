<?php

namespace Myth\View\Renderers;

use Myth\HTTP\Request;
use Myth\View\RendererInterface;
use RuntimeException;

class HTMLRenderer implements RendererInterface
{
    private ?string $content = null;
    private array $data = [];
    private Request $request;

    /**
     * Creates a new HTMLRenderer instance with a Request object set.
     */
    public static function createWithRequest(Request $request): static
    {
        $renderer = new static();
        $renderer->withRequest($request);

        return $renderer;
    }

    /**
     * Generates the output for the given route file.
     * At this point, the control file has already been loaded and executed,
     * and the results of the control can be set with the `withRouteParams` method.
     *
     * NOTE: The route file is the full path to the file.
     */
    public function render(string $routeFile): ?string
    {
        $hasRequest = $this->request instanceof Request;

        $content = $this->view($routeFile, $hasRequest);

        // If we don't have a request object, then we're just loading the view
        // directly and don't need to worry about layouts.
        if (! $hasRequest) {
            return $content;
        }

        // Non-boosted HTMX pages should not have a layout either
        // so it can return the partials that get loaded into the page.
        if ($this->request->isHtmx() && ! $this->request->isBoosted()) {
            return $content;
        }

        // Otherwise, we need to display a full HTML page with layouts, since
        // we're either boosted or a fresh page load.
        $layoutContent = $this->craftLayout($routeFile);

        if (empty($layoutContent)) {
            return $content;
        }

        // TODO: Allow for multiple named slots in the layout
        return str_replace('<slot></slot>', $content, $layoutContent);
    }

    /**
     * Sets the content and data to be used when rendering the view.
     * This is generated by the control file, if one exists.
     */
    public function withRouteParams(string $content, array $data = []): self
    {
        $this->content = $content;
        $this->data = $data;

        return $this;
    }

    /**
     * Sets the Request object to be used when rendering the view.
     */
    public function withRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Renders a single PHP file, returning the generated HTML.
     * If either $content or $data are set, they will be available to the view.
     */
    private function view(string $file): string
    {
        if (! file_exists($file)) {
            throw new \RuntimeException("View not found: {$file}");
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

    /**
     * Crafts the layout for the page.
     *
     * TODO: needs to recursively search the directories to the current level.
     * @return string
     */
    private function craftLayout(string $routeFile): string
    {
        $layoutFile = dirname($routeFile) .'/+layout.php';

        if (! file_exists($layoutFile)) {
            return '';
        }

        return $this->view($layoutFile);
    }
}
