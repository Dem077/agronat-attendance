<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon enabled
    |--------------------------------------------------------------------------
    |
    | Horizon only runs on production when HORIZON_ENABLED=true. Local/dev
    | should keep this false and use QUEUE_CONNECTION=database with
    | `php artisan queue:work` when you need to process jobs locally.
    |
    */

    'enabled' => filter_var(env('HORIZON_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Horizon access permission (Spatie)
    |--------------------------------------------------------------------------
    |
    | Assign this permission to any role via Roles → edit role checkboxes.
    |
    */

    'permission' => env('HORIZON_PERMISSION', 'horizon-view'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    */

    'domain' => env('HORIZON_DOMAIN'),

    'path' => env('HORIZON_PATH', 'horizon'),

    'use' => 'default',

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    'middleware' => ['web'],

    'waits' => [
        'redis:default' => 60,
    ],

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => false,

    'memory_limit' => 64,

    'defaults' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'maxProcesses' => 1,
            'memory' => 128,
            'tries' => 3,
            'timeout' => 90,
            'nice' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Production workers only
    |--------------------------------------------------------------------------
    |
    | Do not add a "local" block — Horizon is not run on Windows/dev.
    | APP_ENV on the live server must be "production".
    |
    */

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'maxProcesses' => (int) env('HORIZON_MAX_PROCESSES', 5),
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
        ],
    ],
];
