<?php

declare(strict_types=1);

use function Cake\Core\env;

return [
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT', 'change-this-in-real-project'),
    ],

    'Datasources' => [
        'default' => [
            'host' => env('DB_HOST', 'db'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'cake'),
            'password' => env('DB_PASSWORD', 'cake'),
            'database' => env('DB_DATABASE', 'cakephp_app'),
            'url' => env('DATABASE_URL', null),
        ],
        'test' => [
            'host' => env('DB_HOST', 'db'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'cake'),
            'password' => env('DB_PASSWORD', 'cake'),
            'database' => env('DB_TEST_DATABASE', 'cakephp_test'),
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    'DebugKit' => [
        'safeTld' => ['test', 'localhost', 'invalid', 'example', 'internal', 'space'],
    ],
];
