<?php
declare(strict_types=1);

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

return [
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT', 'change-this-in-real-project'),
    ],

    'Datasources' => [
        'default' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'host' => env('DB_HOST', 'db'),
            'port' => (int)env('DB_PORT', 3306),
            'username' => env('DB_USERNAME', 'cake'),
            'password' => env('DB_PASSWORD', 'cake'),
            'database' => env('DB_DATABASE', 'cakephp_app'),
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
        ],
        'test' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'host' => env('DB_HOST', 'db'),
            'port' => (int)env('DB_PORT', 3306),
            'username' => env('DB_USERNAME', 'cake'),
            'password' => env('DB_PASSWORD', 'cake'),
            'database' => env('DB_TEST_DATABASE', 'cakephp_test'),
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
        ],
    ],
];
