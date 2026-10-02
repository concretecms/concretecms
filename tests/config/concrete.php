<?php

return [
    'cache' => [
        'doctrine_dev_mode' => true,
        'enabled' => false,
        'overrides' => false,
        'pages' => false,
        'blocks' => false,
    ],
    'user' => [
        'password' => [
            'hash_cost_log2' => 4,
        ],
        'email' => [
            // Needed because of a bug in 1.1.x versions of Egulias\EmailValidator which throws a "Undefined variable: dns" warning if this isn't set
            'test_mx_record' => true,
        ],
    ],
    'log' => [
        'configuration' => [
            'simple' => [
                // a test case that creates no table has no Logs one to write to
                'handler' => 'file',
                'file' => [
                    'file' => DIR_TESTS . '/logs/tests.log',
                ],
            ],
        ],
    ],
    'misc' => [
        // Let's lower the PNG compression, so that tests run faster
        'default_png_image_compression' => 5,
    ],
    'messenger' => [

        'routing' => [
            'Concrete\Core\Foundation\Command\AsyncCommandInterface' => ['sync'],
        ],
    ],
];
