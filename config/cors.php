<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:8100',
        'http://localhost',
        'http://127.0.0.1',
        'capacitor://localhost',
        'ionic://localhost',
        'https://frontend-ruddy-iota-49.vercel.app',
        'https://santeko.abdatytch.com', // Sans espace et sans /api/ à la fin
        'http://72.61.145.76:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => true,
];