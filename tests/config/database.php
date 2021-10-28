<?php
/**
 * Test database configuration.
 */
return [
    'default-connection' => 'travis',
    'connections' => [
        'travis' => [
            'driver' => 'concrete_pdo_mysql',
            'server' => '127.0.0.1',
            'database' => 'concrete5_tests',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
        'travisWithoutDB' => [
            'driver' => 'concrete_pdo_mysql',
            'server' => '127.0.0.1',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
    ],
];
