<?php

use Concrete\Core\Application\Application;

/*
 * ----------------------------------------------------------------------------
 * Instantiate concrete5
 * ----------------------------------------------------------------------------
 */
$app = new Application();

/*
 * ----------------------------------------------------------------------------
 * Detect the environment based on the hostname of the server
 * ----------------------------------------------------------------------------
 */
$app->detectEnvironment(
    array(
        'local' => array(
            'hostname',
        ),
        'production' => array(
            'live.site',
        ),
    ));

return $app;
