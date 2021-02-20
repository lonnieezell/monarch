<?php
    include '../myth/bootstrap.php';
    view()->extends('layout');
?>

<?= view()->startSlot('main') ?>

    <h1>Index File</h1>

<?= view()->endSlot() ?>

<?= view()->render() ?>
