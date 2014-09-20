<?php
/**
 * ----------------------------------------------------------------------------
 * Set our own version of __DIR__ as $__DIR__ so we can include this file on PHP < 5.3 and have it not
 * die wholesale.
 * ----------------------------------------------------------------------------
 */
$__DIR__ = dirname(__FILE__);



/**
 * ----------------------------------------------------------------------------
 * Set required constants, including directory names, attempt to include site configuration file with database
 * information, attempt to determine if we ought to skip to an updated core, etc...
 * ----------------------------------------------------------------------------
 */
require $__DIR__ . '/bootstrap/configure.php';



/**
 * ----------------------------------------------------------------------------
 * Include all autoloaders
 * ----------------------------------------------------------------------------
 */
require $__DIR__ . '/bootstrap/autoload.php';



/**
 * ----------------------------------------------------------------------------
 * Begin concrete5 startup.
 * ----------------------------------------------------------------------------
 */
$cms = require $__DIR__ . '/bootstrap/start.php';


if (!$cms->isRunThroughCommandLineInterface()) {

    /**
     * ----------------------------------------------------------------------------
     * Shut it down.
     * ----------------------------------------------------------------------------
     */
    $cms->shutdown();

} else {
    return $cms;
}
