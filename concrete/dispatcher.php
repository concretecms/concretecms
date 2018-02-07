<?php

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50509) {
    die("concrete5 requires PHP 5.5.9+ to run.\nYou are running PHP " . PHP_VERSION . "\n");
}

/*
 * ----------------------------------------------------------------------------
 * Set required constants, including directory names, attempt to include site configuration file with database
 * information, attempt to determine if we ought to skip to an updated core, etc...
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/bootstrap/configure.php';

/*
 * ----------------------------------------------------------------------------
 * Include all autoloaders.
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/bootstrap/autoload.php';

/*
 * ----------------------------------------------------------------------------
 * Begin concrete5 startup.
 * ----------------------------------------------------------------------------
 */
$app = require __DIR__ . '/bootstrap/start.php';
/** @var \Concrete\Core\Application\Application $app */

/*
 * ----------------------------------------------------------------------------
 * Run the runtime.
 * ----------------------------------------------------------------------------
 */
$runtime = $app->getRuntime();
if ($response = $runtime->run()) {

    /*
     * ------------------------------------------------------------------------
     * Shut it down.
     * ------------------------------------------------------------------------
     */
    $app->shutdown();
} else {
    return $app;
}
