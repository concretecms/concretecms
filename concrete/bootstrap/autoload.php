<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Load all composer autoload items.
 * ----------------------------------------------------------------------------.
 */
include DIR_APPLICATION . '/bootstrap/autoload.php';

/**
 * ----------------------------------------------------------------------------
 * Load the concrete5 class loader and trigger it.
 * ----------------------------------------------------------------------------.
 */
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ClassLoaderInterface.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ModifiedPsr4ClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/Psr4ClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/AliasClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/MapClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ClassLoader.php';
require DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Foundation/ClassAliasList.php';
$class_loader = \Concrete\Core\Foundation\ClassLoader::getInstance();

$enable_legacy_src_namespace = false;
$app_config_path = DIR_APPLICATION . '/config/app.php';
if (file_exists($app_config_path)) {
    $app_config = require $app_config_path;
    if (isset($app_config['namespace'])) {
        $namespace = $app_config['namespace'];
    }
    if (isset($app_config['enable_legacy_src_namespace'])) {
        $enable_legacy_src_namespace = $app_config['enable_legacy_src_namespace'];
    }
}
if (isset($namespace)) {
    $class_loader->setApplicationNamespace($namespace);
}
if ($enable_legacy_src_namespace) {
    $class_loader->enableLegacyNamespace();
}
$class_loader->enable();