<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Load all composer autoload items.
 * ----------------------------------------------------------------------------
 */
if (!@include(DIR_BASE_CORE . '/' . DIRNAME_VENDOR . '/autoload.php')) {
	die('Third party libraries not installed. Make sure that composer has required libraries in the concrete/ directory.');
}



/**
 * ----------------------------------------------------------------------------
 * Load the concrete5 class loader and trigger it.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ModifiedPsr4ClassLoader.php';
\Concrete\Core\Foundation\ClassLoader::getInstance();