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
 * Registries for theme paths, assets, routes and file types.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/config/theme_paths.php';
require DIR_BASE_CORE . '/config/assets.php';
require DIR_BASE_CORE . '/config/routes.php';
require DIR_BASE_CORE . '/config/file_types.php';



/**
 * ----------------------------------------------------------------------------
 * Check the page cache in case we need to return a result early.
 * ----------------------------------------------------------------------------
 */
$request = Request::getInstance();
$response = $cms->checkPageCache($request);
if ($response) {
	$response->send();
	$cms->shutdown();
}


/**
 * ----------------------------------------------------------------------------
 * Return the CMS object.
 * ----------------------------------------------------------------------------
 */
return $cms;


