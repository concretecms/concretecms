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
use Concrete\Core\Config\DatabaseLoader;
use Concrete\Core\Config\DatabaseSaver;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\FileSaver;
use Concrete\Core\Config\Repository\Repository as ConfigRepository;
use Concrete\Core\File\Type\TypeList;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use Patchwork\Utf8\Bootup as PatchworkUTF8;

/**
 * ----------------------------------------------------------------------------
 * Handle text encoding.
 * ----------------------------------------------------------------------------
 */
PatchworkUTF8::initAll();

/**
 * ----------------------------------------------------------------------------
 * Instantiate concrete5.
 * ----------------------------------------------------------------------------
 */
/** @var Application $cms */
$cms = require DIR_APPLICATION . '/bootstrap/start.php';
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
 * Load path detection for relative assets, URL and path to home.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/bootstrap/paths.php';


/**
 * ----------------------------------------------------------------------------
 * Add install environment detection
 * ----------------------------------------------------------------------------
 */
if (file_exists(DIR_APPLICATION . '/config/database.php')) {
    $db_config = include DIR_APPLICATION . '/config/database.php';
}
$environment = $cms->environment();
$cms->detectEnvironment(function() use ($db_config, $environment, $cms) {
    try {
        $installed = $cms->isInstalled();
        return $installed;
    } catch (\Exception $e) {}

    return isset($db_config['default-connection']) ? $environment : 'install';
});

/**
 * ----------------------------------------------------------------------------
 * Enable Filesystem Config.
 * ----------------------------------------------------------------------------
 */
if (!$cms->bound('config')) {
    $file_system = new Filesystem();
    $file_loader = new FileLoader($file_system);
    $file_saver = new FileSaver($file_system);
    $cms->instance('config', new ConfigRepository($file_loader, $file_saver, $cms->environment()));
}

$config = $cms->make('config');

/**
 * ----------------------------------------------------------------------------
 * Timezone Config
 * ----------------------------------------------------------------------------
 */
if (!$config->has('app.timezone')) {
    // There is no timezone set.
    $config->set('app.timezone', @date_default_timezone_get());
}

if (!$config->has('app.server_timezone')) {
    // There is no server timezone set.
    $config->set('app.server_timezone', @date_default_timezone_get());
}

@date_default_timezone_set($config->get('app.timezone'));

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
 * Set up Database Config.
 * ----------------------------------------------------------------------------
 */

if (!$cms->bound('config/database')) {
    $database_loader = new DatabaseLoader();
    $database_saver = new DatabaseSaver();
    $cms->instance('config/database', new ConfigRepository($database_loader, $database_saver, $cms->environment()));
}

$database_config = $cms->make('config/database');

/**
 * ----------------------------------------------------------------------------
 * Setup the core service groups.
 * ----------------------------------------------------------------------------
 */
$list = new ProviderList($cms);
$list->registerProviders($config->get('app.providers'));

/**
 * ----------------------------------------------------------------------------
 * Legacy Definitions
 * ----------------------------------------------------------------------------
 */
define('APP_VERSION', $config->get('concrete.version'));
define('APP_CHARSET', $config->get('concrete.charset'));
define('BASE_URL', \Core::getApplicationURL());
define('DIR_REL', $cms['app_relative_path']);


/**
 * ----------------------------------------------------------------------------
 * Setup file cache directories. Has to come after we define services
 * because we use the file service.
 * ----------------------------------------------------------------------------
 */
$cms->setupFilesystem();

/**
 * ----------------------------------------------------------------------------
 * Registries for theme paths, assets, routes and file types.
 * ----------------------------------------------------------------------------
 */
$asset_list = AssetList::getInstance();

$asset_list->registerMultiple($config->get('app.assets', array()));
$asset_list->registerGroupMultiple($config->get('app.asset_groups', array()));

Route::registerMultiple($config->get('app.routes'));
Route::setThemesByRoutes($config->get('app.theme_paths', array()));

$type_list = TypeList::getInstance();
$type_list->defineMultiple($config->get('app.file_types', array()));
$type_list->defineImporterAttributeMultiple($config->get('app.importer_attributes', array()));

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
 * If not through CLI, load up the application/bootstrap/app.php
 */
include DIR_APPLICATION . '/bootstrap/app.php';


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
 * Load preprocess items
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/bootstrap/preprocess.php';

/**
 * ----------------------------------------------------------------------------
 * Set the active language for the site, based either on the site locale, or the
 * current user record. This can be changed later as well, during runtime.
 * Start localization library.
 * ----------------------------------------------------------------------------
 */
$u = new User();
$lan = $u->getUserLanguageToDisplay();
$loc = Localization::getInstance();
$loc->setLocale($lan);

/**
 * Handle automatic updating
 */
$cms->handleAutomaticUpdates();

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
 * Fire an event for intercepting the dispatch
 * ----------------------------------------------------------------------------
 */
\Events::dispatch('on_before_dispatch');

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
