<?php
/**
 * Test database configuration
 */
return [
    'default-connection' => 'travis',
    'connections' => [
        'travis' => [
            'driver' => 'c5_pdo_mysql',
            'server' => '127.0.0.1',
            'database' => 'concrete5_tests',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
        'travisWithoutDB' => [
            'driver' => 'c5_pdo_mysql',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ]
    ]
];
