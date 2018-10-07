<?php

return [
    'drivers' => [
        'c5_pdo_mysql' => '\Concrete\Core\Database\Driver\PDOMySqlConcrete5\Driver',
    ],

    /*
     * The location of the doctrine Proxy Classes
     */
    'proxy_classes' => DIR_CONFIG_SITE . '/doctrine/proxies',

    /*
     * Paths to exclude from the doctrine proxy classes
     */
    'proxy_exclusions' => [
        DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Support/',
    ],

    /*
     * Workaround for not being able to define indexes on TEXT fields with the current version of Doctrine DBAL.
     * This feature will be removed when DBAL will support it, so don't use this feature.
     */
    'text_indexes' => [
        'PagePaths' => [
            'cPath' => [
                ['cPath', 255],
            ],
        ],
        'Groups' => [
            'gPath' => [
                ['gPath', 255],
            ],
        ],
    ],

    // The preferred database character set (will fallback to 'utf8' if unavailable)
    'preferred_character_set' => 'utf8mb4',

    // The preferred database collation - leave empty to use the default collation associated to the character set
    // (will fallback to 'utf8_general_ci' preferred_character_set is unavailable)
    'preferred_collation' => '',
];
