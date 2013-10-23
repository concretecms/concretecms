<?php
/** 
 * This constant ensures that we're operating inside dispatcher.php. There is a LATER check to ensure that dispatcher.php is being called correctly. ##
 */
if (!defined("C5_EXECUTE")) {
	define('C5_EXECUTE', true);
}

/** 
 * Some naughty error suppression
 */
if(defined("E_DEPRECATED")) {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); // E_DEPRECATED required for php 5.3.0 because of depreciated function calls in 3rd party libs (adodb).
} else {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
}

/** 
 * Early loading items here.
 */
require __DIR__ . '/config/base_pre.php';
require __DIR__ . '/config/base.php';
require __DIR__ . '/startup/required.php';
require __DIR__ . '/startup/autoload.php';
require __DIR__ . '/startup/updated_core_check.php';

/** 
 * Create the app container
 */
$app = Dispatcher::get();

/** 
 * Startup
 */
$app->bootstrap();

/** 
 * Get the current request
 */
$request = Request::getInstance();

/** 
 * Start the application
 */
$app->start($request);

/** 
 * Get the response to the current request
 */
$response = $app->dispatch();

/** 
 * Send it to the user
 */
$response->send();

/**
 * Shut it down
 */
$app->shutdown();