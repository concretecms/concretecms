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
use Loader;
use Log;
use Package;
use Page;
use Redirect;
use \Concrete\Core\Http\Request;
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

    /**
     * Turns off the lights.
     * @param array $options Array of options for disabling certain things during shutdown
     *      Add `'jobs' => true` to disable scheduled jobs
     *      Add `'log_queries' => true` to disable query logging
     */
    public function shutdown($options = array())
    {
        \Events::dispatch('on_shutdown');

        if ($this->isInstalled()) {
            if (!isset($options['jobs']) || $options['jobs'] == false) {
                $this->handleScheduledJobs();
            }

            $logger = new Logger();
            $r = Request::getInstance();

            if (Config::get('concrete.log.queries.log') &&
                (!isset($options['log_queries']) || $options['log_queries'] == false)) {
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
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Config::get('app.curl.verifyPeer'));
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
        Config::set('app.bootstrap.packages_loaded', true);
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
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleURLSlashes(SymfonyRequest $request)
    {
        $url = Url::createFromUrl($request->getUri());
        if ($request->getPathInfo() != '/') {
            if (urldecode((string) $url) != urldecode($request->getUri())) {
                $response = new RedirectResponse((string) $url, 301);
                $response->setRequest($request);
                return $response;
            }
        }
    }

    /**
     * If we have redirect to canonical host enabled, we need to honor it here.
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleCanonicalURLRedirection(SymfonyRequest $r)
    {
        if (Config::get('concrete.seo.redirect_to_canonical_url') && Config::get('concrete.seo.canonical_url')) {
            $url = UrlImmutable::createFromUrl($r->getUri());

            $canonical = UrlImmutable::createFromUrl(\Config::get('concrete.seo.canonical_url'),
                (bool) \Config::get('concrete.seo.trailing_slash')
            );

            // Set the parts of the current URL that are specified in the canonical URL, including host,
            // port, scheme. Set scheme first so that our port can use the magic "set if necessary" method.
            $new = $url->setScheme($canonical->getScheme()->get());
            $new = $new->setHost($canonical->getHost()->get());
            $new = $new->setPortIfNecessary($canonical->getPort()->get());

            // Now we have our current url, swapped out with the important parts of the canonical URL.
            // If it matches, we're good.
            if ($new == $url) {
                return null;
            }

            // Uh oh, it didn't match. before we redirect to the canonical URL, let's check to see if we have an SSL
            // URL
            if (\Config::get('concrete.seo.canonical_ssl_url')) {
                $ssl = UrlImmutable::createFromUrl(\Config::get('concrete.seo.canonical_ssl_url'));

                $new = $url->setScheme($ssl->getScheme()->get());
                $new = $new->setHost($ssl->getHost()->get());
                $new = $new->setPortIfNecessary($ssl->getPort()->get());

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
        $r = Request::getInstance();
        $pos = stripos($r->server->get('SCRIPT_NAME'), DISPATCHER_FILENAME);
        if($pos > 0) {
            //we do this because in CLI circumstances (and some random ones) we would end up with index.ph instead of index.php
            $pos = $pos - 1;
        }
        $home = substr($r->server->get('SCRIPT_NAME'), 0, $pos);
        $this['app_relative_path'] = rtrim($home, '/');

        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $detector = new EnvironmentDetector();
        return $this->environment = $detector->detect($environments, $args);
    }

}
