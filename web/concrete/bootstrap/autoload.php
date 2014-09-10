<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Load all composer autoload items.
 * ----------------------------------------------------------------------------
 */
include DIR_APPLICATION . '/bootstrap/autoload.php';

/**
 * ----------------------------------------------------------------------------
 * Load the concrete5 class loader and trigger it.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ModifiedPsr4ClassLoader.php';
\Concrete\Core\Foundation\ClassLoader::getInstance();
