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
            'hash_cost_log2' => 1,
        ],
        'email' => [
            // Needed because of a bug in 1.1.x versions of Egulias\EmailValidator which throws a "Undefined variable: dns" warning if this isn't set
            'test_mx_record' => true,
        ],
    ],
    'misc' => [
        // Let's lower the PNG compression, so that tests run faster
        'default_png_image_compression' => 5,
    ],
];
