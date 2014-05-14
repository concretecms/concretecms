<?php
namespace Concrete\Core\Application;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Foundation\ClassLoader;
use Core;
use Database;
use Environment;
use Illuminate\Container\Container;
use Job;
use JobSet;
use Loader;
use \Concrete\Core\Logging\GroupLogger;
use Package;
use Page;
use Redirect;
use Request;
use Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use User;
use View;

/**
 * Class Application
 *
 * @package Concrete\Core\Application
 */
class Application extends Container
{

    /**
     * @var bool
     */
    protected $installed = false;

    /**
     * Initializes concrete5
     */
    public function __construct()
    {
        if (defined('CONFIG_FILE_EXISTS')) {
            $this->installed = true;
        }
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Deal with application exceptions.
     */
    public function handleExceptions()
    {
        $app = $this;
        set_exception_handler(
            function ($e) use ($app) {
                // log if setup to do so
                if (defined('ENABLE_LOG_ERRORS') && ENABLE_LOG_ERRORS) {
                    $db = Database::get();
                    if ($db->isConnected()) {
                        $l = new GroupLogger(LOG_TYPE_EXCEPTIONS, Logger::CRITICAL);
                        $l->write(
                          t('Exception Occurred: ') . sprintf(
                              "%s:%d %s (%d)\n",
                              $e->getFile(),
                              $e->getLine(),
                              $e->getMessage(),
                              $e->getCode()
                          )
                        );
                        $l->write($e->getTraceAsString());
                        $l->close();
                    }
                }

                if (defined('SITE_DEBUG_LEVEL') && SITE_DEBUG_LEVEL == DEBUG_DISPLAY_ERRORS || (!defined(
                        'SITE_DEBUG_LEVEL'
                    ))
                ) {
                    Core::make('helper/concrete/ui')->renderError(
                        t('An unexpected error occurred.'),
                        $e->getMessage(),
                        $e
                    );
                } else {
                    Core::make('helper/concrete/ui')->renderError(
                        t('An unexpected error occurred.'),
                        t('An error occurred while processing this request.')
                    );
                }

                $app->shutdown();
            }
        );
    }

    /**
     * Turns off the lights.
     */
    public function shutdown()
    {
        $this->handleScheduledJobs();
        $db = Database::get();
        if ($db->isConnected()) {
            $db->close();
        }
        if (defined('ENABLE_OVERRIDE_CACHE') && ENABLE_OVERRIDE_CACHE) {
            Environment::saveCachedEnvironmentObject();
        } else {
            if (defined('ENABLE_OVERRIDE_CACHE') && (!ENABLE_OVERRIDE_CACHE)) {
                $env = Environment::get();
                $env->clearOverrideCache();
            }
        }
        exit;
    }

    /**
     * If we have job scheduling running through the site, we check to see if it's time to go for it.
     */
    protected function handleScheduledJobs()
    {
        if ($this->isInstalled() && ENABLE_JOB_SCHEDULING) {
            $c = Page::getCurrentPage();
            if ($c instanceof Page && !$c->isAdminArea()) {
                // check for non dashboard page
                $jobs = Job::getList(true);
                $auth = Job::generateAuth();
                $url = "";
                // jobs
                if (count($jobs)) {
                    foreach ($jobs as $j) {
                        if ($j->isScheduledForNow()) {
                            $url = BASE_URL . View::url(
                                                  '/tools/required/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID(
                                                  )
                                );
                            break;
                        }
                    }
                }

                // job sets
                if (!strlen($url)) {
                    $jSets = JobSet::getList();
                    if (is_array($jSets) && count($jSets)) {
                        foreach ($jSets as $set) {
                            if ($set->isScheduledForNow()) {
                                $url = BASE_URL . View::url(
                                                      '/tools/required/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID(
                                                      )
                                    );
                                break;
                            }
                        }
                    }
                }

                if (strlen($url)) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    $res = curl_exec($ch);
                }
            }
        }
    }

    /**
     * Returns true if concrete5 is installed, false if it has not yet been
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * Checks to see whether we should deliver a concrete5 response from the page cache
     */
    public function checkPageCache(Request $request)
    {
        $library = PageCache::getLibrary();
        if ($library->shouldCheckCache($request)) {
            $record = $library->getRecord($request);
            if ($record instanceof PageCacheRecord) {
                if ($record->validate()) {
                    return $library->deliver($record);
                }
            }
        }
        return false;
    }

    /**
     * Run startup and localization events on any installed packages.
     */
    public function setupPackages()
    {
        $pla = \Concrete\Core\Package\PackageList::get();
        $pl = $pla->getPackages();
        $cl = ClassLoader::getInstance();
        foreach ($pl as $p) {
            if ($p->isPackageInstalled()) {
                $pkg = Package::getClass($p->getPackageHandle());
                if (is_object($pkg)) {
                    $cl->registerPackage($pkg);
                    // handle updates
                    if (ENABLE_AUTO_UPDATE_PACKAGES) {
                        $pkgInstalledVersion = $p->getPackageVersion();
                        $pkgFileVersion = $pkg->getPackageVersion();
                        if (version_compare($pkgFileVersion, $pkgInstalledVersion, '>')) {
                            $currentLocale = Localization::activeLocale();
                            if ($currentLocale != 'en_US') {
                                Localization::changeLocale('en_US');
                            }
                            $p->upgradeCoreData();
                            $p->upgrade();
                            if ($currentLocale != 'en_US') {
                                Localization::changeLocale($currentLocale);
                            }
                        }
                    }
                    $pkg->setupPackageLocalization();
                    if (method_exists($pkg, 'on_start')) {
                        $pkg->on_start();
                    }
                }
            }
        }
    }

    /**
     * Ensure we have a cache directory
     */
    public function setupFilesystem()
    {
        if (!defined('FILE_PERMISSIONS_MODE')) {
            $perm = $this->make('helper/file')->getCreateFilePermissions()->file;
            $perm ? define('FILE_PERMISSIONS_MODE', $perm) : define('FILE_PERMISSIONS_MODE', 0664);
        }
        if (!defined('DIRECTORY_PERMISSIONS_MODE')) {
            $perm = $this->make('helper/file')->getCreateFilePermissions()->dir;
            $perm ? define('DIRECTORY_PERMISSIONS_MODE', $perm) : define('DIRECTORY_PERMISSIONS_MODE', 0775);
        }
        if (defined('DIR_FILES_CACHE') && !is_dir(DIR_FILES_CACHE)) {
            @mkdir(DIR_FILES_CACHE);
            @chmod(DIR_FILES_CACHE, DIRECTORY_PERMISSIONS_MODE);
            @touch(DIR_FILES_CACHE . '/index.html');
            @chmod(DIR_FILES_CACHE . '/index.html', FILE_PERMISSIONS_MODE);
        }
    }

    /**
     * Returns true if the app is run through the command line
     */
    public function isRunThroughCommandLineInterface()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * Using the configuration value, determines whether we need to redirect to a URL with
     * a trailing slash or not.
     *
     * @return void
     */
    public function handleURLSlashes()
    {
        $r = Request::getInstance();
        $pathInfo = $r->getPathInfo();
        if (strlen($pathInfo) > 1) {
            $path = trim($pathInfo, '/');
            $redirect = '/' . $path;
            if (URL_USE_TRAILING_SLASH) {
                $redirect .= '/';
            }
            if ($pathInfo != $redirect) {
                Redirect::url(
                        BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/' . $path . ($r->getQueryString(
                        ) ? '?' . $r->getQueryString() : '')
                )->send();
            }
        }
    }

    /**
     * If we have REDIRECT_TO_BASE_URL enabled, we need to honor it here.
     */
    public function handleBaseURLRedirection()
    {
        if (REDIRECT_TO_BASE_URL) {
            $protocol = 'http://';
            $base_url = BASE_URL;
            if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
                $protocol = 'https://';
                if (defined('BASE_URL_SSL')) {
                    $base_url = BASE_URL_SSL;
                }
            }

            $uri = $this->make('security')->sanitizeURL($_SERVER['REQUEST_URI']);
            if (strpos($uri, '%7E') !== false) {
                $uri = str_replace('%7E', '~', $uri);
            }

            if (($base_url != $protocol . $_SERVER['HTTP_HOST']) && ($base_url . ':' . $_SERVER['SERVER_PORT'] != 'https://' . $_SERVER['HTTP_HOST'])) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $base_url . $uri);
                exit;
            }
        }
    }

    /**
     * Inspects the request and determines what to serve.
     */
    public function dispatch(Request $request)
    {
        if ($this->installed) {
            $response = $this->getEarlyDispatchResponse();
        }
        if (!isset($response)) {
            $this->addRequiredRoutes();
            $collection = Route::getList();
            $context = new \Symfony\Component\Routing\RequestContext();
            $context->fromRequest($request);
            $matcher = new UrlMatcher($collection, $context);
            $path = rtrim($request->getPathInfo(), '/') . '/';
            $request->attributes->add($matcher->match($path));
            $matched = $matcher->match($path);
            $route = $collection->get($matched['_route']);
            Route::setRequest($request);
            $response = Route::execute($route, $matched);
        }
        return $response;
    }

    /**
     * @return Response
     */
    protected function getEarlyDispatchResponse()
    {
        if (!User::isLoggedIn()) {
            User::verifyAuthTypeCookie();
        }
        if (User::isLoggedIn()) {
            // check to see if this is a valid user account
            $u = new User();
            $valid = $u->checkLogin();
            if (!$valid) {
                $isActive = $u->isActive();
                $u->logout();
                if (!$isActive) {
                    return Redirect::to('/login', 'account_deactivated')->send();
                } else {
                    $v = new View('/frontend/user_error');
                    $v->setViewTheme('concrete');
                    $contents = $v->render();
                    return new Response($contents, 403);
                }
            }
        }
    }

    /**
     *  Adds a few required routes to the dispatcher that must come at the end.
     */
    protected function addRequiredRoutes()
    {
        Route::register('/', 'dispatcher', 'home');
        Route::register('{path}', 'dispatcher', 'page', array('path' => '.+'));
    }

}
