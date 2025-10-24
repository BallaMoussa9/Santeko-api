<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcast Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connection names defined in the "connections" array below.
    |
    | Supported: "reverb", "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to your client applications. An example has been
    | set up for you for both Pusher and Redis based broadcasters.
    |
    | Most of the connection options will be passed directly to the driver's
    | client, so you may pass any additional client options as needed.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'host' => env('PUSHER_HOST'),
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'reverb' => [
            'driver' => 'reverb',
            'host' => env('REVERB_HOST', 'localhost'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'app_id' => env('REVERB_APP_ID'),
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'force_tls' => env('REVERB_SCHEME', 'http') === 'https',
            'ssl_cert' => env('REVERB_SSL_CERTIFICATE'),
            'ssl_key' => env('REVERB_SSL_KEY'),
            'ssl_passphrase' => env('REVERB_SSL_PASSPHRASE'),
            'sync' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
