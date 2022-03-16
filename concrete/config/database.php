<?php

return [
    'drivers' => [
        'c5_pdo_mysql' => '\Concrete\Core\Database\Driver\PDOMySqlConcrete\Driver',
        'concrete_pdo_mysql' => '\Concrete\Core\Database\Driver\PDOMySqlConcrete\Driver',
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

    'redis' => [

        'parameters' => [],
        'options' => []
    ],


    // The preferred database character set (derived from preferred_collation if empty)
    'preferred_character_set' => '',

    // The preferred database collation (derived from preferred_character_set if empty)
    'preferred_collation' => 'utf8mb4_unicode_ci',

    // The fallback database character set to be used when the preferred one can't be applied
    'fallback_character_set' => 'utf8',

    // The fallback database collation to be used when the preferred one can't be applied
    'fallback_collation' => 'utf8_unicode_ci',
];
