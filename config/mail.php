<?php

use Laminas\Mail\Transport\File as FileTransport;
use Laminas\Math\Rand;

return [
    /*
    |--------------------------------------------------------------------------
    | From Address
    |--------------------------------------------------------------------------
    |
    | The default user that will be used when sending emails if no one
    | is specified when sending the email.
    */
    'from' => [
        'name' => env('MAIL_FROM_NAME', 'Monarch'),
        'address' => env('MAIL_FROM_ADDRESS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    |
    | The transport (driver) that is used to send emails.
    |
    | Supported: "memory", "sendmail", "smtp", "file"
    */
    'default_transport' => env('MAIL_TRANSPORT', 'memory'),

    /*
    |--------------------------------------------------------------------------
    | Transport Options
    |--------------------------------------------------------------------------
    | The options that are used to configure the transport.
    */
    'transport_options' => [
        'smtp' => [
            'host' => env('MAIL_SMTP_HOST', 'localhost'),
            'port' => env('MAIL_SMTP_PORT', 25),
            'connection_class' => env('MAIL_SMTP_CONNECTION_CLASS', 'plain'),
            'connection_config' => [
                'username' => env('MAIL_SMTP_USERNAME', ''),
                'password' => env('MAIL_SMTP_PASSWORD', ''),
                'ssl' => env('MAIL_SMTP_SSL', 'tls'),
            ],
        ],
        'file' => [
            'path'     => WRITEPATH .'app/mail/',
            'callback' => function (FileTransport $transport) {
                return sprintf(
                    'Message_%f_%s.txt',
                    microtime(true),
                    Rand::getString(8)
                );
            },
        ],
    ],
];
