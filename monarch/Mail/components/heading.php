<?php
    $as = strtolower($attributes->get('as', 'h1'));
    $defaultStyles = [
        'h1' => 'font-size: 24px; line-height: 32px; font-weight: bold; margin: 16px 0;',
        'h2' => 'font-size: 20px; line-height: 28px; font-weight: bold; margin: 16px 0;',
        'h3' => 'font-size: 18px; line-height: 26px; font-weight: bold; margin: 16px 0;',
        'h4' => 'font-size: 16px; line-height: 24px; font-weight: bold; margin: 16px 0;',
        'h5' => 'font-size: 14px; line-height: 22px; font-weight: bold; margin: 16px 0;',
        'h6' => 'font-size: 12px; line-height: 20px; font-weight: bold; margin: 16px 0;',
    ];
?>
<<?= $as ?> <?= $attributes->merge([
    'style' => $defaultStyles[$as] ?? $defaultStyles['h1']
]) ?>>
    <x-slot></x-slot>
</<?= $as ?>>
