<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Ici on autorise le front Vite (http://localhost:5173), l'app mobile
    | (http://localhost:8100) et le front Vercel à communiquer avec l'API Laravel.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',   // Front Vite
        'http://localhost:8100',   // Ionic web
        'http://localhost',        // Android WebView
        'http://127.0.0.1',        // fallback
        'capacitor://localhost',   // si nécessaire pour tests
        'ionic://localhost',       // 👈 AJOUTER CETTE LIGNE (Obligatoire pour iOS, utile pour Android)
        'https://frontend-ruddy-iota-49.vercel.app', // Front Vercel
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => true, // nécessaire si tu utilises Sanctum

];
