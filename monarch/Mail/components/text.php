<p
    <?= $attributes->except('style', 'text') ?>
    <?= $attributes->merge([
        'style' => 'font-size: 14px; line-height: 24px; margin: 16px 0;'
    ]) ?>
>
    <x-slot></x-slot>
</p>
