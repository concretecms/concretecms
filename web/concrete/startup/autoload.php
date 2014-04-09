<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** 
 * This file is responsible for making sure that the concrete5 Classloader is ready to go and has the files
 * it needs in order to do so
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/Foundation/Classloader.php';
require __DIR__ . '/../core/Foundation/ModifiedPsr4ClassLoader.php';
require __DIR__ . '/../vendor/zend/Loader/Autoloader.php';

/** 
 * Trigger the start of autoload
 */
\Concrete\Core\Foundation\ClassLoader::getInstance();