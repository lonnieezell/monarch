<?php

/**
 * Tracy Configuration
 *
 * This file contains the configuration options for Tracy.
 * See the Tracy documentation for more information.
 *
 * @see  https://tracy.nette.org/en/configuring
 */
return [
    /*
     * Error Logging
     */
    // 'email' => null,
    // 'fromEmail' => null,
    // 'mailer' => null,
    // 'emailSnooze' => 0,
    'logSeverity' => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED,
    'logDirectory' => WRITEPATH . 'logs',

    /*
     * Dump Behavior
     */
    'maxLength' => 150,
    'maxDepth' => 10,
    'keysToHide' => ['password'],
    'dumpTheme' => 'dark',
    'showLocation' => true,

    /*
     * Other Settings
     */
    'strictMode' => true,
    'scream' => false,
    'editor' => 'vscode://file/%file:%line',
    'errorTemplate' => null,
    'showBar' => true,
];
