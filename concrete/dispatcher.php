<?php

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 70209) {
    die("Concrete requires PHP 7.2.9 to run.\nYou are running PHP " . PHP_VERSION . "\n");
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
 * Make sure you cannot call dispatcher.php directly.
 * ----------------------------------------------------------------------------
 */
if (basename($_SERVER['PHP_SELF']) === DISPATCHER_FILENAME_CORE) {
    die('Access Denied.');
}

/*
 * ----------------------------------------------------------------------------
 * Include all autoloaders.
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/bootstrap/autoload.php';

/*
 * ----------------------------------------------------------------------------
 * Begin Concrete startup.
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
