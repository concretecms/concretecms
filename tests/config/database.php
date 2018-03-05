<?php
/**
 * Test database configuration.
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
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
        'travisWithoutDB' => [
            'driver' => 'c5_pdo_mysql',
            'server' => 'localhost',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
    ],
];
