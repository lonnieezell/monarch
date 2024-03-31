<?php

declare(strict_types=1);

namespace Myth\View;

use Myth\Config;
use Myth\HTTP\Request;
use Myth\View\Renderers\HTMLRenderer;
use RuntimeException;

class Renderer
{
    private ?string $content = null;
    private array $data = [];
    private Request $request;

    /**
     * Creates a new Renderer instance with a Request object set.
     */
    public static function createWithRequest(Request $request): static
    {
        $renderer = new static();
        $renderer->withRequest($request);

        return $renderer;
    }

    /**
     * Generates the output for the given route file.
     * It will determine the renderer to use for the route
     * based on the file extension and the classes in the $extmap property.
     *
     * NOTE: The route file is the full path to the file.
     *
     * @throws RuntimeException
     */
    public function render(string $routeFile): ?string
    {
        $rendererName = null;
        $availableRenderers = Config::factory()->get('routes.renderers');
        foreach ($availableRenderers as $ext => $handler) {
            if (substr($routeFile, -strlen($ext)) === $ext) {
                $rendererName = $handler;
                break;
            }
        }

        if ($rendererName === null) {
            throw new \RuntimeException('No renderer found for file: '. $routeFile);
        }

        return $rendererName::createWithRequest($this->request)
            ->withRouteParams($this->content, $this->data)
            ->render($routeFile);
    }

    /**
     * Sets the content and data to be used when rendering the view.
     */
    public function withRouteParams(string $content, array $data = []): self
    {
        $this->content = $content;
        $this->data = $data;

        return $this;
    }

    /**
     * Sets the Request object to be used when rendering the routes.
     */
    public function withRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}
