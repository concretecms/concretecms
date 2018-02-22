<?php

return [
    'cache' => [
        'doctrine_dev_mode' => true,
        'enabled' => false,
        'overrides' => false,
        'pages' => false,
        'blocks' => false,
    ],
    'misc' => [
        // Let's lower the PNG compression, so that tests run faster
        'default_png_image_compression' => 5,
    ],
];
