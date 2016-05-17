<?php

defined('C5_EXECUTE') or die("Access Denied.");

/*
 * ----------------------------------------------------------------------------
 * Ensure we're not accessing this file directly.
 * ----------------------------------------------------------------------------
 */
if (basename($_SERVER['PHP_SELF']) == DISPATCHER_FILENAME_CORE) {
    die("Access Denied.");
}

/*
 * ----------------------------------------------------------------------------
 * Handle text encoding.
 * ----------------------------------------------------------------------------
 */
\Patchwork\Utf8\Bootup::initAll();

/*
 * ----------------------------------------------------------------------------
 * Instantiate concrete5.
 * ----------------------------------------------------------------------------
 */
/** @var \Concrete\Core\Application\Application $cms */
$cms = require DIR_APPLICATION . '/bootstrap/start.php';
$cms->instance('app', $cms);

// Bind fully application qualified class names
$cms->instance('Concrete\Core\Application\Application', $cms);
$cms->instance('Illuminate\Container\Container', $cms);

// Boot the runtime
$cms->getRuntime()->boot();

return $cms;
