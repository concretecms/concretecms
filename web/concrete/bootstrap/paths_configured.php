<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var $app Concrete\Core\Application\Application */
/* @var $config Concrete\Core\Config\Repository\Repository */

/*
 * ----------------------------------------------------------------------------
 * Calculate the cache relative directory
 * ----------------------------------------------------------------------------
 */
$relCacheDirectory = $config->get('concrete.cache.directory_relative');
if ($relCacheDirectory) {
    // Custom relative directory - Strip ending slashes
    $relCacheDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $relCacheDirectory), '/');
} else {
    // Automatically calculate the relative directory
    $fullCacheDirectory = $config->get('concrete.cache.directory');
    if ($fullCacheDirectory === DIR_FILES_UPLOADED_STANDARD . '/cache') {
        // Standard path
        $relCacheDirectory = REL_DIR_FILES_UPLOADED_STANDARD . '/cache';
    } else {
        // Custom path - Must be under the webroot
        $fullCacheDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $fullCacheDirectory), '/');
        $fullRootDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', DIR_BASE), '/');
        if (strpos($fullCacheDirectory, $fullRootDirectory) !== 0) {
            echo 'The cache directory must be published under the webroot (or you can set the concrete.cache.directory_relative configuration option)';
            die(1);
        }
        $relCacheDirectory = substr($fullCacheDirectory, strlen($fullRootDirectory) - 1);
        unset($fullRootDirectory);
    }
    unset($fullCacheDirectory);
}
define('REL_DIR_FILES_CACHE', $relCacheDirectory);
unset($relCacheDirectory);
