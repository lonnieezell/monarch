<?php
    $target = $attributes->get('target', '_blank');
    [$styles, $pt, $pr, $pb, $pl] = $parsePadding($attributes->get('style'));

    $parsePadding = function(string|array $styles)
    {
        if (is_string($styles)) {
            $styles = explode(';', $styles);
        }

        $pt = $pr = $pb = $pl = 0;

        $parsedStyles = array_reduce($styles, function($carry, $style)
        use(&$pt, &$pr, &$pb, &$pl) {
            [$key, $value] = explode(':', $style);
            $key = trim($key);
            $value = trim($value);

            // Parse the padding values out of the styles
            // and don't include this in the final styles array
            if ($key === 'padding') {
                $padding = explode(' ', $value);
                $pt = $padding[0];
                $pr = $padding[1] ?? $pt;
                $pb = $padding[2] ?? $pt;
                $pl = $padding[3] ?? $pr;
            } else {
                // Else add the style to the final styles array
                $carry[$key] = $value;
            }

            return $carry;
        }, []);

        return [$parsedStyles, $pt, $pr, $pb, $pl];
    };

    $buttonStyle = function(array $styles, string $pt, string $pr, string $pb, string $pl) {
        $buttonStyles = array_merge([
            'background-color' => '#3490dc',
            'color' => '#ffffff',
            'line-height' => '100%',
            'text-decoration' => 'none',
            'display' => 'inline-block',
            'max-width' => '100%',
            'mso-padding-alt' => '0px',
            'margin-left' => 'auto',
            'margin-right' => 'auto',
            "padding: {$pt}px {$pr}px {$pb}px {$pl}px",
        ], $styles);

        // convert the array into a string of CSS styles
        return implode('; ', array_map(function($key, $value) {
            return "{$key}: {$value}";
        }, array_keys($buttonStyles), $buttonStyles));
    };

    $buttonTextStyle = function(string $pb) {
        $textStyles = [
            'max-width' => '100%',
            'display' => 'inline-block',
            'line-height' => '120%',
            'mso-padding-alt' => '0px',
            'padding-bottom' => "{$pb}px",
        ];

        // convert the array into a string of CSS styles
        return implode('; ', array_map(function($key, $value) {
            return "{$key}: {$value}";
        }, array_keys($textStyles), $textStyles));
    };
?>
<a
    <?= $attributes->except('style', 'target') ?>
    target="<?= $target ?>"
    style="<?= $buttonStyle($styles, $pt, $pr, $pb, $pl) ?>"
>
    <span style="<?= $buttonTextStyle($pb) ?>">
        <x-slot></x-slot>
    </span>
</a>
