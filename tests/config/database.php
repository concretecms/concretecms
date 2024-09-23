<?php
/**
 * Test database configuration.
 */
return [
    'default-connection' => 'ccm_test',
    'connections' => [
        'ccm_test' => [
            'driver' => 'concrete_pdo_mysql',
            'server' => '127.0.0.1',
            'database' => 'ccm_tests',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
        'ccm_testWithoutDB' => [
            'driver' => 'concrete_pdo_mysql',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
            ],
        ],
    ],
];
