<?php

namespace Concrete\Core\Application;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Cache\Page\PageCacheRecord;
use Concrete\Core\Cache\OpCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Foundation\EnvironmentDetector;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Query\Logger;
use Concrete\Core\Routing\DispatcherRouteCallback;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Updater\Update;
use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlImmutable;
use Config;
use Core;
use Database;
use Environment;
use Illuminate\Container\Container;
use Job;
use JobSet;
use Log;
use Package;
use Page;
use Redirect;
use Concrete\Core\Http\Request;
use Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use User;
use View;

class Application extends Container
{
    protected $installed = null;
    protected $environment = null;
    protected $packages = array();

    /**
     * Turns off the lights.
     *
     * @param array $options Array of options for disabling certain things during shutdown
     *      Add `'jobs' => true` to disable scheduled jobs
     *      Add `'log_queries' => true` to disable query logging
     */
    public function shutdown($options = array())
    {
        \Events::dispatch('on_shutdown');

        $config = $this['config'];

        if ($this->isInstalled()) {
            if (!isset($options['jobs']) || $options['jobs'] == false) {
                $this->handleScheduledJobs();
            }

            $logger = new Logger();
            $r = Request::getInstance();

            if ($config->get('concrete.log.queries.log') &&
                (!isset($options['log_queries']) || $options['log_queries'] == false)) {
                $connection = Database::getActiveConnection();
                if ($logger->shouldLogQueries($r)) {
                    $loggers = array();
                    $configuration = $connection->getConfiguration();
                    $loggers[] = $configuration->getSQLLogger();
                    $configuration->setSQLLogger(null);
                    if ($config->get('concrete.log.queries.clear_on_reload')) {
                        $logger->clearQueryLog();
                    }

                    $logger->write($loggers);
                }
            }

            foreach (\Database::getConnections() as $connection) {
                $connection->close();
            }
        }
        if ($config->get('concrete.cache.overrides')) {
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

        $this['cache']->flush();
        $this['cache/expensive']->flush();

        $config = $this['config'];

        // Delete and re-create the cache directory
        $cacheDir = $config->get('concrete.cache.directory');
        if (is_dir($cacheDir)) {
            $fh = Core::make('helper/file');
            $fh->removeAll($cacheDir, true);
        }
        $this->setupFilesystem();

        $pageCache = PageCache::getLibrary();
        if (is_object($pageCache)) {
            $pageCache->flush();
        }

        // Clear the file thumbnail path cache
        $connection = $this['database'];
        $sql = $connection->getDatabasePlatform()->getTruncateTableSQL('FileImageThumbnailPaths');
        try {
            $connection->executeUpdate($sql);
        } catch(\Exception $e) {}

        // clear the environment overrides cache
        $env = \Environment::get();
        $env->clearOverrideCache();

        // Clear localization cache
        Localization::clearCache();

        // clear block type cache
        BlockType::clearCache();

        // Clear precompiled script bytecode caches
        OpCache::clear();

        \Events::dispatch('on_cache_flush_end');
    }

    /**
     * If we have job scheduling running through the site, we check to see if it's time to go for it.
     */
    protected function handleScheduledJobs()
    {
        $config = $this['config'];

        if ($config->get('concrete.jobs.enable_scheduling')) {
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
                            $url = View::url(
                                                  '/ccm/system/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID(
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
                                $url = View::url(
                                                      '/ccm/system/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID(
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
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $config->get('app.curl.verifyPeer'));
                    $res = curl_exec($ch);
                }
            }
        }
    }

    /**
     * Returns true if concrete5 is installed, false if it has not yet been.
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
     * Checks to see whether we should deliver a concrete5 response from the page cache.
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
        $config = $this['config'];

        if ($config->get('concrete.updates.enable_auto_update_core')) {
            $installed = $config->get('concrete.version_installed');
            $core = $config->get('concrete.version');
            if ($core && $installed && version_compare($installed, $core, '<')) {
                Update::updateToCurrentVersion();
            }
        }
    }

    /**
     * Register package autoloaders. Has to come BEFORE session calls.
     */
    public function setupPackageAutoloaders()
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
                    $this->packages[] = $pkg;
                }
            }
        }
    }
    /**
     * Run startup and localization events on any installed packages.
     */
    public function setupPackages()
    {
        $checkAfterStart = false;

        $config = $this['config'];

        foreach($this->packages as $pkg) {
            // handle updates
            if ($config->get('concrete.updates.enable_auto_update_packages')) {
                $dbPkg = \Package::getByHandle($pkg->getPackageHandle());
                $pkgInstalledVersion = $dbPkg->getPackageVersion();
                $pkgFileVersion = $pkg->getPackageVersion();
                if (version_compare($pkgFileVersion, $pkgInstalledVersion, '>')) {
                    $currentLocale = Localization::activeLocale();
                    if ($currentLocale != 'en_US') {
                        Localization::changeLocale('en_US');
                    }
                    $dbPkg->upgradeCoreData();
                    $dbPkg->upgrade();
                    if ($currentLocale != 'en_US') {
                        Localization::changeLocale($currentLocale);
                    }
                }
            }
            $pkg->setupPackageLocalization();
        }
        foreach($this->packages as $pkg) {
            if (method_exists($pkg, 'on_start')) {
                $pkg->on_start();
            }
            if (method_exists($pkg, 'on_after_packages_start')) {
                $checkAfterStart = true;
            }
        }
        $config->set('app.bootstrap.packages_loaded', true);
        \Localization::setupSiteLocalization();

        if ($checkAfterStart) {
            foreach($this->packages as $pkg) {
                if (method_exists($pkg, 'on_after_packages_start')) {
                    $pkg->on_after_packages_start();
                }
            }
        }
    }

    /**
     * Ensure we have a cache directory.
     */
    public function setupFilesystem()
    {
        $config = $this['config'];

        if (!is_dir($config->get('concrete.cache.directory'))) {
            @mkdir($config->get('concrete.cache.directory'), $config->get('concrete.filesystem.permissions.directory'));
            @touch($config->get('concrete.cache.directory') . '/index.html', $config->get('concrete.filesystem.permissions.file'));
        }
    }

    /**
     * Returns true if the app is run through the command line.
     */
    public static function isRunThroughCommandLineInterface()
    {
        return defined('C5_ENVIRONMENT_ONLY') && C5_ENVIRONMENT_ONLY || PHP_SAPI == 'cli';
    }

    /**
     * Using the configuration value, determines whether we need to redirect to a URL with
     * a trailing slash or not.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleURLSlashes(SymfonyRequest $request)
    {
        $trailing_slashes = $this['config']['concrete.seo.trailing_slash'];
        $path = $request->getPathInfo();

        // If this isn't the homepage
        if ($path && $path != '/') {

            // If the trailing slash doesn't match the config, return a redirect response
            if (($trailing_slashes && substr($path, -1) != '/') ||
                (!$trailing_slashes && substr($path, -1) == '/')) {

                $parsed_url = Url::createFromUrl($request->getUri(),
                $trailing_slashes ? Url::TRAILING_SLASHES_ENABLED : Url::TRAILING_SLASHES_DISABLED);

                $response = new RedirectResponse($parsed_url, 301);
                $response->setRequest($request);

                return $response;
            }
        }
    }

    /**
     * If we have redirect to canonical host enabled, we need to honor it here.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleCanonicalURLRedirection(SymfonyRequest $r)
    {
        $config = $this['config'];

        if ($config->get('concrete.seo.redirect_to_canonical_url') && $config->get('concrete.seo.canonical_url')) {
            $url = UrlImmutable::createFromUrl($r->getUri());

            $canonical = UrlImmutable::createFromUrl($config->get('concrete.seo.canonical_url'),
                (bool) $config->get('concrete.seo.trailing_slash')
            );

            // Set the parts of the current URL that are specified in the canonical URL, including host,
            // port, scheme. Set scheme first so that our port can use the magic "set if necessary" method.
            $new = $url->setScheme($canonical->getScheme()->get());
            $new = $new->setHost($canonical->getHost()->get());
            $new = $new->setPort($canonical->getPort()->get());

            // Now we have our current url, swapped out with the important parts of the canonical URL.
            // If it matches, we're good.
            if ($new == $url) {
                return null;
            }

            // Uh oh, it didn't match. before we redirect to the canonical URL, let's check to see if we have an SSL
            // URL
            if ($config->get('concrete.seo.canonical_ssl_url')) {
                $ssl = UrlImmutable::createFromUrl($config->get('concrete.seo.canonical_ssl_url'));

                $new = $url->setScheme($ssl->getScheme()->get());
                $new = $new->setHost($ssl->getHost()->get());
                $new = $new->setPort($ssl->getPort()->get());

                // Now we have our current url, swapped out with the important parts of the canonical URL.
                // If it matches, we're good.
                if ($new == $url) {
                    return null;
                }
            }

            $response = new RedirectResponse($new, '301');

            return $response;
        }
    }

    /**
     * Inspects the request and determines what to serve.
     */
    public function dispatch(Request $request)
    {
        // This is a crappy place for this, but it has to come AFTER the packages because sometimes packages
        // want to replace legacy "tools" URLs with the new MVC, and the tools paths are so greedy they don't
        // work unless they come at the end.
        $this->registerLegacyRoutes();


        $path = rawurldecode($request->getPathInfo());

        if (strpos($path, '..') !== false) {
            throw new \RuntimeException(t('Invalid path traversal. Please make this request with a valid HTTP client.'));
        }

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
            } catch (ResourceNotFoundException $e) {
                $callback = new DispatcherRouteCallback('dispatcher');
                $response = $callback->execute($request);
            }
        }

        return $response;
    }

    protected function registerLegacyRoutes()
    {

        \Route::register("/tools/blocks/{btHandle}/{tool}",
            '\Concrete\Core\Legacy\Controller\ToolController::displayBlock',
            'blockTool',
            array('tool' => '[A-Za-z0-9_/.]+')
        );
        \Route::register("/tools/{tool}", '\Concrete\Core\Legacy\Controller\ToolController::display',
        '   tool',
            array('tool' => '[A-Za-z0-9_/.]+')
        );
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
                if ($u->isError()) {
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
     *
     * @return string|bool
     */
    public function environment()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this->environment, func_get_args());
        } else {
            return $this->environment;
        }
    }

    /**
     * Detect the application's current environment.
     *
     * @param  array|string|Callable  $environments
     *
     * @return string
     */
    public function detectEnvironment($environments)
    {
        $r = Request::getInstance();
        $pos = stripos($r->server->get('SCRIPT_NAME'), DISPATCHER_FILENAME);
        if ($pos > 0) {
            //we do this because in CLI circumstances (and some random ones) we would end up with index.ph instead of index.php
            $pos = $pos - 1;
        }
        $home = substr($r->server->get('SCRIPT_NAME'), 0, $pos);
        $this['app_relative_path'] = rtrim($home, '/');

        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $detector = new EnvironmentDetector();

        return $this->environment = $detector->detect($environments, $args);
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  string $concrete
     * @param  array $parameters
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function build($concrete, $parameters = array())
    {
        $object = parent::build($concrete, $parameters);
        if (is_object($object) && $object instanceof ApplicationAwareInterface) {
            $object->setApplication($this);
        }

        return $object;
    }

}
