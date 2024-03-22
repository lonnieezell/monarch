<?php

declare(strict_types=1);

namespace Myth\Routes;

class Renderer
{
    private string $content;
    private array $data;
    private array $extmap = [
        '.api.php' => 'renderAPI',
        '.php' => 'renderPHP',
        '.md' => 'renderMarkdown',
    ];

    public function render(string $routeFile): ?string
    {
        $output = '';

        $methodName = null;
        foreach ($this->extmap as $ext => $method) {
            if (substr($routeFile, -strlen($ext)) === $ext) {
                $methodName = $method;
                break;
            }
        }

        if ($methodName !== null) {
            $output = $this->{$methodName}($routeFile);
        }

        return $output;
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
     * Renders a PHP file, allowing for the use of PHP code within the view.
     */
    private function renderPHP(string $routeFile): string
    {
        // Ensure that the data is available to the view
        extract([
            'content' => $this->content,
            'data' => $this->data,
        ]);

        ob_start();
        include $routeFile;
        return ob_get_clean();
    }

    /**
     * Renders an API file as JSON. The file should return an array of data.
     */
    private function renderAPI(string $routeFile): string
    {
        $output = null;

        // Ensure that the data is available to the view
        extract([
            'content' => $this->content,
            'data' => $this->data,
        ]);

        $output = include $routeFile;

        // Set the response content type to JSON
        header('Content-Type: application/json');

        return json_encode($output);
    }

    private function renderMarkdown(string $routeFile): string
    {
        return file_get_contents($routeFile);
    }
}
