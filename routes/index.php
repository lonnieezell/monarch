<?php

use Monarch\Debug;

    viewMeta()
        ->setTitle('Welcome to the Monarch Framework')
        ->addMeta([
            'description' => 'The Monarch Framework is a simple, lightweight PHP framework for building web applications.'
        ]);
?>

<x-hero>
    <h1 class="display-5 fw-bold text-body-emphasis">Welcome to Monarch</h1>
    <div class="col-lg-6 mx-auto">
    <p class="lead mb-4">Monarch is an opinionated framework for building the web.</p>
    </div>
</x-hero>
