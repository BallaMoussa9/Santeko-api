<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Ici on autorise le front Vite (http://localhost:5173) à communiquer avec
    | ton API Laravel (http://localhost). Ça résout ton problème de CORS.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // pas besoin de login/logout ici

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173', 'http://localhost:8100'], // front Vue/Vite

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => true, // nécessaire si tu utilises Sanctum
];
