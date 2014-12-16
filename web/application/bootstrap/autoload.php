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
* autoload Application\Core namespaced classes from the application/src/ directory
*/
spl_autoload_register(function($class){
    $prefix = "Application\\Core";
    // does the class use the Application\Core namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) { // check if this is in our namespace or not
        return;
    }

    $relative_class = substr($class, $len);
    $base_dir = DIR_APPLICATION . "/src";
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});