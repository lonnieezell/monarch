<table
    align="center"
    width="100%"
    border="0"
    cellPadding="0"
    cellSpacing="0"
    role="presentation"
    <?= $attributes->merge([
        'style' => 'max-width: 37.5em; margin: 0 auto;',
    ]) ?>
>
    <tbody>
        <tr style="width: 100%">
            <td>
                <x-slot></x-slot>
            </td>
        </tr>
    </tbody>
</table>
