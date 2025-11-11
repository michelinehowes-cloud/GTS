<?php

return [
    'exports' => [
        'store_path' => storage_path('app/exports'),
    ],

    'imports' => [
        'read_only' => true,
        'ignore_empty' => false,
        'heading_row' => [
            'formatter' => 'slug',
        ],
    ],

    'extension_detector' => [
        'xlsx' => 'Xlsx',
        'xls' => 'Xls',
        'csv' => 'Csv',
    ],

    'value_binder' => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    'transactions' => [
        'handler' => 'db',
        'db' => [
            'connection' => null,
        ],
    ],

    'temporary_files' => [
        'local_path' => storage_path('framework/cache/laravel-excel'),
        'remote_disk' => null,
        'remote_prefix' => null,
        'force_resync_remote' => null,
    ],
];