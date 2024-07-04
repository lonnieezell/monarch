<?php

namespace Monarch\View;

use Laminas\Escaper\Escaper as LaminasEscaper;

class Escaper
{
    private static self $instance;
    private LaminasEscaper $escaper;

    /**
     * Gets a singleton instance
     * @return Escaper
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->escaper = new LaminasEscaper(config('app.outputEncoding'));
    }

    /**
     * Escapes a string for HTML output
     *
     * Used when outputting user-contributed content to the
     * HTML body of a page.
     *
     * Example:
     *  <?= escapeHtml($userInput) ?>
     */
    public function escapeHtml(string $string): string
    {
        return $this->escaper->escapeHtml($string);
    }

    /**
     * Escapes a string for HTML attribute output
     *
     * Used when outputting user-contributed content to an
     * on attribute value of an HTML element.
     *
     * Example:
     * <input type="text" value="<?= escapeHtmlAttr($userInput) ?>">
     */
    public function escapeHtmlAttr(string $string): string
    {
        return $this->escaper->escapeHtmlAttr($string);
    }

    /**
     * Escapes a string for JavaScript output
     *
     * Used when outputting user-contributed content within
     * a <script> body.
     *
     * Example:
     * <script>
     *   var userInput = "<?= escapeJs($userInput) ?>";
     * </script>
     */
    public function escapeJs(string $string): string
    {
        return $this->escaper->escapeJs($string);
    }

    /**
     * Escapes a string for CSS output
     *
     * Used when outputting user-contributed content within
     * a <style> tag.
     *
     * Example:
     * <style>
     *  .user-input {
     *     content: "<?= escapeCss($userInput) ?>";
     * }
     * </style>
     */
    public function escapeCss(string $string): string
    {
        return $this->escaper->escapeCss($string);
    }

    /**
     * Escapes a string for URL output
     *
     * Used when outputting user-contributed content within
     * a URL, not for the entire URL.
     *
     * Example:
     * <a href="http:://example.com?q=<?= escapeUrl($userInput) ?>">Link</a>
     */
    public function escapeUrl(string $string): string
    {
        return $this->escaper->escapeUrl($string);
    }
}
