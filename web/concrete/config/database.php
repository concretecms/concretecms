<?php

return array(
    'drivers' => array(
        'c5_pdo_mysql' => '\Concrete\Core\Database\Driver\PDOMySqlConcrete5\Driver',
    ),

    /*
     * The location of the doctrine Proxy Classes
     */
    'proxy_classes' => DIR_APPLICATION . '/config/doctrine/proxies',

    /*
     * Paths to exclude from the doctrine proxy classes
     */
    'proxy_exclusions' => array(
        DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Support/',
    ),
);
