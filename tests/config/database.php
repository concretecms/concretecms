<?php
/**
 * Test database configuration
 */
return [
    'default-connection' => 'travis',
    'connections' => [
        'travis' => [
            'driver' => 'c5_pdo_mysql',
            'server' => 'localhost',
            'database' => 'concrete5_tests',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
        'travisWithoutDB' => [
            'driver' => 'c5_pdo_mysql',
            'server' => 'localhost',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ]
    ]
];
