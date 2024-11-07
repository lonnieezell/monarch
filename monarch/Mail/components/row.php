<table
    align="center"
    width="100%"
    border="0"
    cellPadding="0"
    cellSpacing="0"
    role="presentation"
    <?= $attributes->merge([
        'style' => 'border-collapse: collapse;'
    ]) ?>
>
<tbody style="width: 100%">
    <tr style="width: 100%">
        <x-slot></x-slot>
    </tr>
</tbody>
</table>
