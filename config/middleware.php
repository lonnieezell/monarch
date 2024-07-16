<?php

return [
    //The default middleware group to use
    // when no middleware is specified.
    'default' => 'web',

    // The middleware classes to run through
    // for standard 'web' requests.
    'web' => [
        \Monarch\HTTP\Middleware\Security::class,
        \Monarch\HTTP\Middleware\Debugger::class,
    ],

    // The middleware classes to run through
    // for API requests.
    'api' => [
        //
    ],
];
