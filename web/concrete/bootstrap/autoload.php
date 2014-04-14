<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Load all composer autoload items.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/' . DIRNAME_VENDOR . '/autoload.php';



/**
 * ----------------------------------------------------------------------------
 * Load the Zend Autoloader
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/' . DIRNAME_VENDOR . '/zend/Loader/Autoloader.php';



/**
 * ----------------------------------------------------------------------------
 * Load the concrete5 class loader and trigger it.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/Classloader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ModifiedPsr4ClassLoader.php';
\Concrete\Core\Foundation\ClassLoader::getInstance();