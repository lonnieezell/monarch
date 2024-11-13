<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Component Paths
    |--------------------------------------------------------------------------
    |
    | The paths to search for components in. These paths are relative to the
    | root of the application. The 'key' is the prefix for the tag name.
    |
    | Default: [
    |   'x' => 'app/Components',
    |   'm' => 'monarch/Mail/components',
    | ]
    */
    'componentPaths' => [
        'x' => 'app/Components',
        'm' => MONARCHPATH .'Mail/components',
    ],

    /*
    |--------------------------------------------------------------------------
    | Encoding of HTML output
    |--------------------------------------------------------------------------
    |
    | The encoding to use for the output of the HTML. This is used by the
    | Escaper class to determine the encoding to use when escaping strings.
    |
    | Default: UTF-8
    */
    'outputEncoding' => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Session Handler
    |--------------------------------------------------------------------------
    |
    | Sets the session handler to use for the application. This can be set to
    | 'files', 'sqlite', or 'redis' to use the respective session handlers.
    | For sqlite or redis to work, the respective extensions must be installed.
    |
    | Default: 'files'
    */
    'sessionHandler' => env('SESSION_HANDLER', 'files'),
    'sessionSavePath' => env('SESSION_SAVE_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | A list of validation rule classes to register with the validation factory.
    |
    | Default: []
    |
    | Example:
    | 'rules' => [
    |     'uuid' => \Somnambulist\Components\Validation\Rules\Uuid::class,
    | ]
    */
    'validationRules' => [
        //
    ],
];
