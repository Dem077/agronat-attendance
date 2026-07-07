<?php

use Illuminate\Support\Str;

return [

    'enabled' => filter_var(env('HORIZON_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'permission' => env('HORIZON_PERMISSION', 'horizon-view'),

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
        'redis:attendance' => 30,
        'redis:recompute' => 120,
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

    'memory_limit' => (int) env('HORIZON_MEMORY_LIMIT', 128),

    'defaults' => [
        'supervisor-default' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 1,
            'maxProcesses' => 2,
            'maxTime' => 0,
            'maxJobs' => 500,
            'memory' => 128,
            'tries' => 1,
            'timeout' => 120,
            'sleep' => 1,
            'nice' => 0,
        ],
        'supervisor-attendance' => [
            'connection' => 'redis',
            'queue' => ['attendance'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 2,
            'maxProcesses' => 4,
            'maxTime' => 0,
            'maxJobs' => 1000,
            'memory' => 128,
            'tries' => 1,
            'timeout' => 60,
            'sleep' => 1,
            'nice' => 0,
        ],
        'supervisor-recompute' => [
            'connection' => 'redis',
            'queue' => ['recompute'],
            'balance' => 'simple',
            'minProcesses' => 4,
            'maxProcesses' => 4,
            'maxTime' => 0,
            'maxJobs' => 50,
            'memory' => 256,
            'tries' => 1,
            'timeout' => 600,
            'sleep' => 1,
            'nice' => 0,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'minProcesses' => (int) env('HORIZON_DEFAULT_MIN_PROCESSES', 1),
                'maxProcesses' => (int) env('HORIZON_DEFAULT_MAX_PROCESSES', 2),
                'balanceMaxShift' => 1,
                'balanceCooldown' => 1,
            ],
            'supervisor-attendance' => [
                'minProcesses' => (int) env('HORIZON_ATTENDANCE_MIN_PROCESSES', 2),
                'maxProcesses' => (int) env('HORIZON_ATTENDANCE_MAX_PROCESSES', 4),
                'balanceMaxShift' => 2,
                'balanceCooldown' => 1,
            ],
            'supervisor-recompute' => [
                'balance' => 'simple',
                'minProcesses' => (int) env('HORIZON_RECOMPUTE_MIN_PROCESSES', 4),
                'maxProcesses' => (int) env('HORIZON_RECOMPUTE_MAX_PROCESSES', 4),
            ],
        ],
        'local' => [
            'supervisor-default' => [
                'maxProcesses' => (int) env('HORIZON_DEFAULT_MAX_PROCESSES', 2),
            ],
            'supervisor-attendance' => [
                'minProcesses' => (int) env('HORIZON_ATTENDANCE_MIN_PROCESSES', 2),
                'maxProcesses' => (int) env('HORIZON_ATTENDANCE_MAX_PROCESSES', 4),
            ],
            'supervisor-recompute' => [
                'minProcesses' => (int) env('HORIZON_RECOMPUTE_MIN_PROCESSES', 2),
                'maxProcesses' => (int) env('HORIZON_RECOMPUTE_MAX_PROCESSES', 4),
            ],
        ],
    ],
];
