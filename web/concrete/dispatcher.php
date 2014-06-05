<?php
/**
 * The full dispatcher for concrete5.
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2014 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/** 
 * ----------------------------------------------------------------------------
 * Set required constants, including directory names, attempt to include site configuration file with database
 * information, attempt to determine if we ought to skip to an updated core, etc...
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/bootstrap/configure.php';



/**
 * ----------------------------------------------------------------------------
 * Include all autoloaders
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/bootstrap/autoload.php';


/**
 * ----------------------------------------------------------------------------
 * Begin concrete5 startup.
 * ----------------------------------------------------------------------------
 */
$cms = require __DIR__ . '/bootstrap/start.php';


/**
 * ----------------------------------------------------------------------------
 * Shut it down.
 * ----------------------------------------------------------------------------
 */
$cms->shutdown();

