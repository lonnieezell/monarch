<?php
    $target = $attributes->get('target', '_blank');
?>
<a
    <?= $attributes->except('target')->merge([
        'style' => 'color: #3490dc; text-decoration: none'
    ]) ?>
    target="<?= $target ?>"
>
    <x-slot></x-slot>
</a>
