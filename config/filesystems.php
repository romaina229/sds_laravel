<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'serve'  => true,
            'throw'  => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        // Disque dédié aux factures PDF (privé, accessible via route protégée)
        'factures' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public/factures'),
            'url'        => env('APP_URL') . '/storage/factures',
            'visibility' => 'public',
            'throw'      => false,
        ],

        // Disque dédié au guide Finance Pro (PRIVÉ, jamais lié à /storage) :
        // le seul moyen d'obtenir le fichier est la route protégée par
        // jeton GuideDownloadController::download — voir routes/api.php.
        'guides' => [
            'driver'     => 'local',
            'root'       => storage_path('app/private/guides'),
            'visibility' => 'private',
            'throw'      => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    | php artisan storage:link
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
