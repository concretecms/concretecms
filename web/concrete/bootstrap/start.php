<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * ----------------------------------------------------------------------------
 * Ensure we're not accessing this file directly.
 * ----------------------------------------------------------------------------
 */
if (basename($_SERVER['PHP_SELF']) == DISPATCHER_FILENAME_CORE) {
	die("Access Denied.");
}



/**
 * ----------------------------------------------------------------------------
 * Import relevant classes.
 * ----------------------------------------------------------------------------
 */
use \Concrete\Core\Application\Application;
use \Concrete\Core\Foundation\ClassAliasList;
use \Concrete\Core\Foundation\Service\ProviderList;
use \Concrete\Core\Support\Facade\Facade;
use \Patchwork\Utf8\Bootup;

/**
 * ----------------------------------------------------------------------------
 * Instantiate concrete5.
 * ----------------------------------------------------------------------------
 */
$cms = new Application();
$cms->instance('app', $cms);


/**
 * ----------------------------------------------------------------------------
 * Bind the IOC container to our facades
 * Completely indebted to Taylor Otwell & Laravel for this.
 * ----------------------------------------------------------------------------
 */
Facade::setFacadeApplication($cms);



/**
 * ----------------------------------------------------------------------------
 * Setup core classes aliases.
 * ----------------------------------------------------------------------------
 */
$list = ClassAliasList::getInstance();
$list->registerMultiple(require DIR_BASE_CORE . '/config/aliases.php');
$list->registerMultiple(require DIR_BASE_CORE . '/config/facades.php');



/**
 * ----------------------------------------------------------------------------
 * Setup the core service groups.
 * ----------------------------------------------------------------------------
 */
$list = new ProviderList($cms);
$list->registerProviders(require DIR_BASE_CORE . '/config/services.php');



/**
 * ----------------------------------------------------------------------------
 * Handle trailing slashes/non trailing slashes in URL. Has to come after 
 * we define our core services because our redirect routines use some of those
 * services
 * ----------------------------------------------------------------------------
 */
$cms->handleURLSlashes();
$cms->handleBaseURLRedirection();



/**
 * ----------------------------------------------------------------------------
 * Handle text encoding.
 * ----------------------------------------------------------------------------
 */
Bootup::initAll();



/**
 * ----------------------------------------------------------------------------
 * Registries for theme paths, assets, routes and file types.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/config/theme_paths.php';
require DIR_BASE_CORE . '/config/assets.php';
require DIR_BASE_CORE . '/config/routes.php';
require DIR_BASE_CORE . '/config/file_types.php';



/**
 * ----------------------------------------------------------------------------
 * If we are running through the command line, we don't proceed any further
 * ----------------------------------------------------------------------------
 */
if ($cms->isRunThroughCommandLineInterface()) {
	return $cms;
}



/**
 * ----------------------------------------------------------------------------
 * Obtain the Request object.
 * ----------------------------------------------------------------------------
 */
$request = Request::getInstance();



/**
 * ----------------------------------------------------------------------------
 * If we haven't installed, then we need to reroute.
 * ----------------------------------------------------------------------------
 */
if (!$cms->isInstalled() && !$cms->isRunThroughCommandLineInterface() && !$request->matches('/install/*') && $request->getPath() != '/install') {
	Redirect::to('/install')->send();
}



/**
 * ----------------------------------------------------------------------------
 * Check the page cache in case we need to return a result early.
 * ----------------------------------------------------------------------------
 */
$response = $cms->checkPageCache($request);
if ($response) {
	$response->send();
	$cms->shutdown();
}



/** 
 * ----------------------------------------------------------------------------
 * Get the response to the current request
 * ----------------------------------------------------------------------------
 */
$response = $cms->dispatch($request);



/** 
 * ----------------------------------------------------------------------------
 * Send it to the user
 * ----------------------------------------------------------------------------
 */
$response->send();



/**
 * ----------------------------------------------------------------------------
 * Return the CMS object.
 * ----------------------------------------------------------------------------
 */
return $cms;


