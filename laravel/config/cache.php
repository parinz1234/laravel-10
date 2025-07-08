<?php

use Illuminate\Support\Str;

return [
    // Set to file for simplicity in New Relic POC
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        // Removed memcached, redis, dynamodb, and other complex cache stores
        // for New Relic POC simplicity
    ],

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
