<?php
namespace Concrete\Core\Application;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Cache\Page\PageCacheRecord;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Foundation\EnvironmentDetector;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Query\Logger;
use Concrete\Core\Routing\DispatcherRouteCallback;
use Concrete\Core\Updater\Update;
use Config;
use Core;
use Database;
use Environment;
use Illuminate\Container\Container;
use Job;
use JobSet;
use Loader;
use Log;
use Package;
use Page;
use Redirect;
use Request;
use Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use User;
use View;

class Application extends Container
{

    protected $installed = null;
    protected $environment = null;

    /**
     * Turns off the lights.
     */
    public function shutdown()
    {
        \Events::dispatch('on_shutdown');

        if ($this->isInstalled()) {
            $this->handleScheduledJobs();

            $logger = new Logger();
            $r = Request::getInstance();

            if (Config::get('concrete.log.queries.log')) {
                $connection = Database::getActiveConnection();
                if ($logger->shouldLogQueries($r)) {
                    $loggers = array();
                    $configuration = $connection->getConfiguration();
                    $loggers[] = $configuration->getSQLLogger();
                    $configuration->setSQLLogger(null);
                    if (Config::get('concrete.log.queries.clear_on_reload')) {
                        $logger->clearQueryLog();
                    }

                    $logger->write($loggers);

                }
            }

            foreach (\Database::getConnections() as $connection) {
                $connection->close();
            }
        }
        if (Config::get('concrete.cache.overrides')) {
            Environment::saveCachedEnvironmentObject();
        } else {
            $env = Environment::get();
            $env->clearOverrideCache();
        }
        exit;
    }

    /**
     * Utility method for clearing all application caches.
     */
    public function clearCaches()
    {
        \Events::dispatch('on_cache_flush');

        Core::make('cache')->flush();
        Core::make('cache/expensive')->flush();

        // flush the CSS cache
        if (is_dir(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
            $fh = Loader::helper("file");
            $fh->removeAll(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
        }

        $pageCache = PageCache::getLibrary();
        if (is_object($pageCache)) {
            $pageCache->flush();
        }

        // clear the environment overrides cache
        $env = \Environment::get();
        $env->clearOverrideCache();

        // Clear localization cache
        Localization::clearCache();

        // clear block type cache
        BlockType::clearCache();
    }

    /**
     * If we have job scheduling running through the site, we check to see if it's time to go for it.
     */
    protected function handleScheduledJobs()
    {
        if (Config::get('concrete.jobs.enable_scheduling')) {
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
        if ($this->installed === null) {
            if (!$this->isShared('config')) {
                throw new \Exception('Attempting to check install status before application initialization.');
            }

            $this->installed = $this->make('config')->get('concrete.installed');
        }

        return $this->installed;
    }

    /**
     * Checks to see whether we should deliver a concrete5 response from the page cache
     */
    public function checkPageCache(\Concrete\Core\Http\Request $request)
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

    public function handleAutomaticUpdates()
    {
        if (Config::get('concrete.updates.enable_auto_update_core')) {
            $installed = Config::get('concrete.version_installed');
            $core = Config::get('concrete.version');
            if ($core && $installed && version_compare($installed, $core, '<')) {
                Update::updateToCurrentVersion();
            }
        }
    }

    /**
     * Run startup and localization events on any installed packages.
     */
    public function setupPackages()
    {
        $pla = \Concrete\Core\Package\PackageList::get();
        $pl = $pla->getPackages();
        $cl = ClassLoader::getInstance();
        /** @var \Package[] $pl */
        foreach ($pl as $p) {
            $p->registerConfigNamespace();
            if ($p->isPackageInstalled()) {
                $pkg = Package::getClass($p->getPackageHandle());
                if (is_object($pkg) && (!$pkg instanceof \Concrete\Core\Package\BrokenPackage)) {
                    $cl->registerPackage($pkg);
                    // handle updates
                    if (Config::get('concrete.updates.enable_auto_update_packages')) {
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
        if (!is_dir(Config::get('concrete.cache.directory'))) {
            @mkdir(Config::get('concrete.cache.directory'), Config::get('concrete.filesystem.permissions.directory'));
            @touch(Config::get('concrete.cache.directory') . '/index.html', Config::get('concrete.filesystem.permissions.file'));
        }
    }

    /**
     * Returns true if the app is run through the command line
     */
    public function isRunThroughCommandLineInterface()
    {

        return defined('C5_ENVIRONMENT_ONLY') && C5_ENVIRONMENT_ONLY || PHP_SAPI == 'cli';
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
            if (Config::get('concrete.seo.trailing_slash')) {
                $redirect .= '/';
            }
            if ($pathInfo != $redirect) {
                $dispatcher = Config::get('concrete.seo.url_rewriting') ? '' : '/' . DISPATCHER_FILENAME;
                Redirect::url(
                        BASE_URL . DIR_REL . $dispatcher . $redirect . ($r->getQueryString(
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
        if (Config::get('concrete.seo.redirect_to_base_url')) {
            $protocol = 'http://';
            $base_url = BASE_URL;
            if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
                $protocol = 'https://';
                if (defined('BASE_URL_SSL')) {
                    $base_url = BASE_URL_SSL;
                }
            }

            $uri = $this->make('helper/security')->sanitizeURL($_SERVER['REQUEST_URI']);
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
            $collection = Route::getList();
            $context = new \Symfony\Component\Routing\RequestContext();
            $context->fromRequest($request);
            $matcher = new UrlMatcher($collection, $context);
            $path = rtrim($request->getPathInfo(), '/') . '/';
            try {
                $request->attributes->add($matcher->match($path));
                $matched = $matcher->match($path);
                $route = $collection->get($matched['_route']);
                Route::setRequest($request);
                $response = Route::execute($route, $matched);
            } catch(ResourceNotFoundException $e) {
                $callback = new DispatcherRouteCallback('dispatcher');
                $response = $callback->execute($request);
            }
        }
        return $response;
    }

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
                if($u->isError()) {
                    switch ($u->getError()) {
                        case USER_SESSION_EXPIRED:
                            return Redirect::to('/login', 'session_invalidated')->send();
                            break;
                    }
                } elseif (!$isActive) {
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
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string|bool
     */
    public function environment()
    {
        if (count(func_get_args()) > 0)
        {
            return in_array($this->environment, func_get_args());
        }
        else
        {
            return $this->environment;
        }
    }

    /**
     * Detect the application's current environment.
     *
     * @param  array|string|Callable  $environments
     * @return string
     */
    public function detectEnvironment($environments)
    {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $detector = new EnvironmentDetector();
        return $this->environment = $detector->detect($environments, $args);
    }

}
