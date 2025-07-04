<?php

declare(strict_types=1);

use Monolog\Level;

return [
    'displayErrorDetails' => env('APP_ENV', 'production') !== 'production', // Should be set to false in production
    'logError'            => env('LOGGER_LOG_ERROR', true),
    'logErrorDetails'     => env('LOGGER_LOG_ERROR_DETAILS', true),
    'logger' => [
        'name' => env('LOGGER_NAME', 'erp-app'),
        'path' => isset($_ENV['docker']) ? 'php://stdout' : storage_path('logs/app.log'),
        'level' => Level::fromName(env('LOGGER_LEVEL', 'Error')),
    ],
    'defaultLang' => env('APP_DEFAULT_LANG', 'en'),
    'database' => [
        /*
        |--------------------------------------------------------------------------
        | Migration Repository Table
        |--------------------------------------------------------------------------
        |
        | This table keeps track of all the migrations that have already run for
        | your application. Using this information, we can determine which of
        | the migrations on disk haven't actually been run in the database.
        |
        */

        'migrations' => 'migrations',

        'default' => env('DB_CONNECTION', 'mysql'),
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'erp_api'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => env('DB_PREFIX', 'erp_'),
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ],
            'sqlite' => [
                'driver'    => 'sqlite',
                'database'  => env('DB_DATABASE', ':memory:'),
                'prefix'    => env('DB_PREFIX', 'erp_'),
            ]
        ],
    ],
    'queue' => [

        /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

        'default' => env('QUEUE_CONNECTION', 'sync'),

        /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

        'connections' => [

            'sync' => [
                'driver' => 'sync',
            ],

            'database' => [
                'driver' => 'database',
                'table' => 'jobs',
                'queue' => 'default',
                'retry_after' => 90,
                'after_commit' => false,
            ],
            'file' => [
                // Path to the JSON file that stores the queue.
                'file_path' => __DIR__ . '/../storage/queue.json',
            ],
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'queue' => env('REDIS_QUEUE', 'default'),
                'retry_after' => 90,
                'block_for' => null,
                'after_commit' => false,
            ],
        ],

        /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

        'batching' => [
            'database' => env('DB_CONNECTION', 'mysql'),
            'table' => 'job_batches',
        ],

        /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

        'failed' => [
            'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
            'database' => env('DB_CONNECTION', 'mysql'),
            'table' => 'failed_jobs',
        ],

    ],
    'filesystems' => [

        /*
        |--------------------------------------------------------------------------
        | Default Filesystem Disk
        |--------------------------------------------------------------------------
        |
        | Here you may specify the default filesystem disk that should be used
        | by the framework. The "local" disk, as well as a variety of cloud
        | based disks are available to your application. Just store away!
        |
        */

        'default' => env('FILESYSTEM_DISK', 's3'),

        /*
        |--------------------------------------------------------------------------
        | Default Cloud Filesystem Disk
        |--------------------------------------------------------------------------
        |
        | Many applications store files both locally and in the cloud. For this
        | reason, you may specify a default "cloud" driver here. This driver
        | will be bound as the Cloud disk implementation in the container.
        |
        */

        'cloud' => env('FILESYSTEM_CLOUD', 's3'),

        /*
        |--------------------------------------------------------------------------
        | Filesystem Disks
        |--------------------------------------------------------------------------
        |
        | Here you may configure as many filesystem "disks" as you wish, and you
        | may even configure multiple disks of the same driver. Defaults have
        | been set up for each driver as an example of the required values.
        |
        | Supported Drivers: "local", "ftp", "sftp", "s3"
        |
        */

        'disks' => [

            'system' => [
                'driver' => 'local',
                'root' => base_path(),
                'throw' => true,
            ],

            'local' => [
                'driver' => 'local',
                'root' => storage_path(env('LOCAL_DISK_ROOT', 'app')),
                'throw' => true,
            ],

            'public' => [
                'driver' => 'local',
                'root' => storage_path(env('PUBLIC_DISK_ROOT', 'app/public')),
                'url' => rtrim((env('APP_URL') ?: ''), '/') . '/storage',
                'visibility' => 'public',
                'throw' => true,
            ],

            's3' => [
                'driver' => 's3',
                'root' => env('AWS_S3_ROOT', ''),
                'key' => env('AWS_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'bucket' => env('AWS_BUCKET', ''),
                'url' => env('AWS_URL', ''),
                'endpoint' => env('AWS_ENDPOINT', ''),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'throw' => true,
            ],
             'gdrive' => [
                'driver' => 'google',
                'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
                'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
                'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
                'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'), // Optional: root folder
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Symbolic Links
        |--------------------------------------------------------------------------
        |
        | Here you may configure the symbolic links that will be created when the
        | `storage:link` Artisan command is executed. The array keys should be
        | the locations of the links and the values should be their targets.
        |
        */

        'links' => [
            public_path('storage') => storage_path('app/public'),
        ],
    ],
    'csrf' => [
        'enabled' => env('APP_CSRF_ENABLED', true),
        'route' => env('APP_CSRF_ROUTE_PATH') ?: '/api/csrf-cookie',
        'excluded_paths' => explode(',', env('APP_CSRF_EXCLUDED_PATHS', '')),
    ]
];
