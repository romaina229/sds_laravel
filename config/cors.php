<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // En production : mettre votre URL Vercel dans FRONTEND_URL
    'allowed_origins' => ['http://localhost:5173',
        env('FRONTEND_URL', 'https://salomdigitalsolutions.vercel.app'),
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,
];
