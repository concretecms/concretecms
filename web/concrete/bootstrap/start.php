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
use Concrete\Core\Application\Application;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Facade;
use Patchwork\Utf8\Bootup;
use Concrete\Core\Config\Config as DatabaseConfig;
use Concrete\Core\File\Type\TypeList;
use Concrete\Core\Config\ConfigLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as ConfigRepository;

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
 * Enable Config
 * ----------------------------------------------------------------------------
 */
$file_system = new Filesystem();
$database_config = new DatabaseConfig();
$file_loader = new ConfigLoader($file_system, $database_config);
$cms->instance('config', $config = new ConfigRepository($file_loader, 'default'));

/**
 * ----------------------------------------------------------------------------
 * Get App version number
 * ----------------------------------------------------------------------------
 */
define('APP_VERSION', $config->get('app.version'));

/**
 * ----------------------------------------------------------------------------
 * Setup core classes aliases.
 * ----------------------------------------------------------------------------
 */
$list = ClassAliasList::getInstance();
$list->registerMultiple($config->get('app.aliases'));
$list->registerMultiple($config->get('app.facades'));

/**
 * ----------------------------------------------------------------------------
 * Setup the core service groups.
 * ----------------------------------------------------------------------------
 */
$list = new ProviderList($cms);
$list->registerProviders($config->get('app.providers'));

/**
 * ----------------------------------------------------------------------------
 * Setup file cache directories. Has to come after we define services
 * because we use the file service.
 * ----------------------------------------------------------------------------
 */
$cms->setupFilesystem();

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
$assets = $config->get('app.assets', array());
$asset_list = AssetList::getInstance();

foreach ($assets as $asset_handle => $asset_types) {
    foreach ($asset_types as $asset_type => $asset_settings) {
        array_splice($asset_settings, 1, 0, $asset_handle);
        call_user_func_array(array($asset_list, 'register'), $asset_settings);
    }
}

$asset_groups = $config->get('app.asset_groups', array());
foreach ($asset_groups as $group_handle => $group_setting) {
    array_unshift($group_setting, $group_handle);
    call_user_func_array(array($asset_list, 'registerGroup'), $group_setting);
}

$theme_paths = $config->get('app.theme_paths');
foreach ($theme_paths as $route => $theme) {
    Route::setThemeByRoute($route, $theme);
}

$routes = $config->get('app.routes');

foreach ($routes as $route => $route_settings) {
    array_unshift($route_settings, $route);
    call_user_func_array(array('Route', 'register'), $route_settings);
}

$type_list = TypeList::getInstance();
$file_types = $config->get('app.file_types');
foreach ($file_types as $type_name => $type_settings) {
    array_splice($type_settings, 1, 0, $type_name);

    call_user_func_array(array($type_list, 'define'), $type_settings);
}

$importer_attributes = $config->get('app.importer_attributes');
foreach ($importer_attributes as $attribute_name => $attribute_settings) {
    array_unshift($attribute_settings, $attribute_name);
    call_user_func_array(array($type_list, 'defineImporterAttribute'), $attribute_settings);
}


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
 * If we haven't installed, then we need to reroute. If we have, and we're
 * on the install page, and we haven't installed, then we need to dispatch
 * early and exit.
 * ----------------------------------------------------------------------------
 */
if (!$cms->isInstalled()) {
    if (!$cms->isRunThroughCommandLineInterface() && !$request->matches('/install/*') && $request->getPath(
        ) != '/install'
    ) {
        $response = Redirect::to('/install');
    }
    else {
        $response = $cms->dispatch($request);
    }
    $response->send();
    $cms->shutdown();
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
 * Include our local config/app.php for any customizations, events, etc...
 * ----------------------------------------------------------------------------
 */
if (file_exists(DIR_CONFIG_SITE)) {
    include DIR_CONFIG_SITE . '/app.php';
}

/**
 * ----------------------------------------------------------------------------
 * Set the active language for the site, based either on the site locale, or the
 * current user record. This can be changed later as well, during runtime.
 * Start localization library.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('SITE_LOCALE', 'en_US');
$u = new User();
$lan = $u->getUserLanguageToDisplay();
$loc = Localization::getInstance();
$loc->setLocale($lan);

/**
 * ----------------------------------------------------------------------------
 * Load database-backed preferences, including items stored in the Config
 * object, localization stuff and dates.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/bootstrap/preferences.php';

/**
 * ----------------------------------------------------------------------------
 * Redirect user based on their trailing or non-trailing slash. Must come after
 * preferences because we use the pretty URLs preference.
 * ----------------------------------------------------------------------------
 */
$cms->handleBaseURLRedirection();
$cms->handleURLSlashes();

/**
 * ----------------------------------------------------------------------------
 * Now we load all installed packages, and run package events on them.
 * ----------------------------------------------------------------------------
 */
$cms->setupPackages();

/**
 * ----------------------------------------------------------------------------
 * Load all permission keys into our local cache.
 * ----------------------------------------------------------------------------
 */
PermissionKey::loadAll();

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
