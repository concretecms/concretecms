<?php
namespace Concrete\Core\Foundation\Runtime\Boot;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\File\Type\TypeList;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Support\Facade\Route;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Illuminate\Config\Repository;
use Symfony\Component\HttpFoundation\Response;

class DefaultBooter implements BootInterface, ApplicationAwareInterface
{
    /** @var Application */
    protected $app;

    /**
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->app = $application;
    }

    /**
     * Boot up
     * Return a response if we're ready to output.
     *
     * @return null|Response
     */
    public function boot()
    {
        $app = $this->app;

        /*
         * ----------------------------------------------------------------------------
         * Bind the IOC container to our facades
         * Completely indebted to Taylor Otwell & Laravel for this.
         * ----------------------------------------------------------------------------
         */
        Facade::setFacadeApplication($app);

        /**
         * ----------------------------------------------------------------------------
         * Load path detection for relative assets, URL and path to home.
         * ----------------------------------------------------------------------------.
         */
        require_once DIR_BASE_CORE . '/bootstrap/paths.php';

        /*
         * ----------------------------------------------------------------------------
         * Add install environment detection
         * ----------------------------------------------------------------------------
         */
        $this->initializeEnvironmentDetection($app);

        /*
         * ----------------------------------------------------------------------------
         * Enable Configuration
         * ----------------------------------------------------------------------------
         */
        $config = $this->initializeConfig($app);

        /*
         * ----------------------------------------------------------------------------
         * Finalize paths.
         * ----------------------------------------------------------------------------
         */
        require DIR_BASE_CORE . '/bootstrap/paths_configured.php';

        /*
         * ----------------------------------------------------------------------------
         * Timezone Config
         * ----------------------------------------------------------------------------
         */
        $this->initializeTimezone($config);

        /*
         * ----------------------------------------------------------------------------
         * Setup core classes aliases.
         * ----------------------------------------------------------------------------
         */
        $this->initializeClassAliases($config);

        /*
         * ----------------------------------------------------------------------------
         * Setup the core service groups.
         * ----------------------------------------------------------------------------
         */
        $this->initializeServiceProviders($app, $config);

        /*
         * ----------------------------------------------------------------------------
         * Legacy Definitions
         * ----------------------------------------------------------------------------
         */
        $this->initializeLegacyDefinitions($config, $app);

        /*
         * ----------------------------------------------------------------------------
         * Setup file cache directories. Has to come after we define services
         * because we use the file service.
         * ----------------------------------------------------------------------------
         */
        $app->setupFilesystem();
        /*
         * ----------------------------------------------------------------------------
         * Registries for theme paths, assets, routes and file types.
         * ----------------------------------------------------------------------------
         */
        $this->initializeAssets($config);
        $this->initializeRoutes($config);
        $this->initializeFileTypes($config);

        // If we're not in the CLI SAPI, lets do additional booting for HTTP
        if (!$this->app->isRunThroughCommandLineInterface()) {
            return $this->bootHttpSapi($config, $app);
        }
    }

    /**
     * @param $config
     * @param $app
     *
     * @return null|Response
     */
    private function bootHttpSapi($config, $app)
    {
        /*
         * ----------------------------------------------------------------------------
         * Initialize the request
         * ----------------------------------------------------------------------------
         */
        $request = $this->initializeRequest($config);

        /*
         * ----------------------------------------------------------------------------
         * If we haven't installed, then we need to reroute. If we have, and we're
         * on the install page, and we haven't installed, then we need to dispatch
         * early and exit.
         * ----------------------------------------------------------------------------
         */
        if ($response = $this->checkInstall($app, $request)) {
            return $response;
        }

        if ($this->app->isInstalled()) {
            /*
             * ----------------------------------------------------------------------------
             * Check the page cache in case we need to return a result early.
             * ----------------------------------------------------------------------------
             */
            if ($response = $this->checkCache($app, $request)) {
                return $response;
            }

            /*
             * ----------------------------------------------------------------------------
             * Now we load all installed packages, and register their package autoloaders.
             * ----------------------------------------------------------------------------
             */
            $this->initializePackages($app);

            /**
             * ----------------------------------------------------------------------------
             * Load preprocess items
             * ----------------------------------------------------------------------------.
             */
            require DIR_BASE_CORE . '/bootstrap/preprocess.php';

            /*
             * ----------------------------------------------------------------------------
             * Set the active language for the site, based either on the site locale, or the
             * current user record. This can be changed later as well, during runtime.
             * Start localization library.
             * ----------------------------------------------------------------------------
             */
            $this->initializeLocalization();

            /*
             * Handle automatic updating
             */
            $app->handleAutomaticUpdates();
        }
    }

    /**
     * Enable configuration.
     *
     * @param Application $app
     *
     * @return Repository
     */
    private function initializeConfig(Application $app)
    {
        $config_provider = $app->make('Concrete\Core\Config\ConfigServiceProvider');
        $config_provider->register();

        /*
         * @var \Concrete\Core\Config\Repository\Repository
         */
        $config = $app->make('config');

        return $config;
    }

    /**
     * @param Application $app
     */
    private function initializeEnvironmentDetection(Application $app)
    {
        $db_config = array();
        if (file_exists(DIR_APPLICATION . '/config/database.php')) {
            $db_config = include DIR_APPLICATION . '/config/database.php';
        }
        $environment = $app->environment();
        $app->detectEnvironment(function () use ($db_config, $environment, $app) {
            try {
                $installed = $app->isInstalled();

                return $installed;
            } catch (\Exception $e) {
            }

            return isset($db_config['default-connection']) ? $environment : 'install';
        });
    }

    /**
     * @param Repository $config
     */
    private function initializeTimezone(Repository $config)
    {
        if (!$config->has('app.timezone')) {
            // There is no timezone set.
            $config->set('app.timezone', @date_default_timezone_get());
        }

        if (!$config->has('app.server_timezone')) {
            // There is no server timezone set.
            $config->set('app.server_timezone', @date_default_timezone_get());
        }

        @date_default_timezone_set($config->get('app.timezone'));
    }

    /**
     * @param Repository $config
     *
     * @return ClassAliasList
     */
    private function initializeClassAliases(Repository $config)
    {
        $list = ClassAliasList::getInstance();
        $list->registerMultiple($config->get('app.aliases'));
        $list->registerMultiple($config->get('app.facades'));

        // Autoload some aliases to prevent typehinting errors
        class_exists('\Request');

        return $list;
    }

    /**
     * @param Application $app
     * @param Repository $config
     */
    private function initializeServiceProviders(Application $app, Repository $config)
    {
        /** @var ProviderList $list */
        $list = $this->app->make('Concrete\Core\Foundation\Service\ProviderList');

        // Register events first so that they can be used by other providers.
        $list->registerProvider($config->get('app.providers.core_events'));

        // Register all other providers
        $list->registerProviders($config->get('app.providers'));
    }

    /**
     * @param Repository $config
     * @param Application $app
     */
    private function initializeLegacyDefinitions(Repository $config, Application $app)
    {
        define('APP_VERSION', $config->get('concrete.version'));
        define('APP_CHARSET', $config->get('concrete.charset'));
        try {
            define('BASE_URL', (string) $this->app->make('url/canonical'));
        } catch (\Exception $x) {
            echo $x->getMessage();
            die(1);
        }
        define('DIR_REL', $app['app_relative_path']);
    }

    /**
     * @param Repository $config
     */
    private function initializeAssets(Repository $config)
    {
        $asset_list = AssetList::getInstance();

        $asset_list->registerMultiple($config->get('app.assets', array()));
        $asset_list->registerGroupMultiple($config->get('app.asset_groups', array()));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     */
    private function initializeRoutes(Repository $config)
    {
        Route::registerMultiple($config->get('app.routes'));
        Route::setThemesByRoutes($config->get('app.theme_paths', array()));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     */
    private function initializeFileTypes(Repository $config)
    {
        $type_list = TypeList::getInstance();
        $type_list->defineMultiple($config->get('app.file_types', array()));
        $type_list->defineImporterAttributeMultiple($config->get('app.importer_attributes', array()));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     *
     * @return Request
     */
    private function initializeRequest(Repository $config)
    {
        /*
         * ----------------------------------------------------------------------------
         * Set trusted proxies and headers for the request
         * ----------------------------------------------------------------------------
         */
        if ($proxyHeaders = $config->get('concrete.security.trusted_proxies.headers')) {
            foreach ($proxyHeaders as $key => $value) {
                Request::setTrustedHeaderName($key, $value);
            }
        }

        if ($trustedProxiesIps = $config->get('concrete.security.trusted_proxies.ips')) {
            Request::setTrustedProxies($trustedProxiesIps);
        }

        /*
         * ----------------------------------------------------------------------------
         * Obtain the Request object.
         * ----------------------------------------------------------------------------
         */
        $request = Request::getInstance();

        return $request;
    }

    /**
     * If we haven't installed and we're not looking at the install directory, redirect.
     *
     * @param Application $app
     * @param Request $request
     *
     * @return null|Response
     */
    private function checkInstall(Application $app, Request $request)
    {
        if (!$app->isInstalled()) {
            if (!$request->matches('/install/*') &&
                $request->getPath() != '/install') {
                $manager = $app->make('Concrete\Core\Url\Resolver\Manager\ResolverManager');
                $response = new RedirectResponse($manager->resolve(array('install')));

                return $response;
            }
        }
    }

    /**
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Http\Request $request
     *
     * @return null|Response
     */
    private function checkCache(Application $app, Request $request)
    {
        $response = $app->checkPageCache($request);
        if ($response) {
            return $response;
        }
    }

    /**
     * @param \Concrete\Core\Application\Application $app
     */
    private function initializePackages(Application $app)
    {
        $app->setupPackageAutoloaders();
    }

    /**
     * Initialize localization.
     */
    private function initializeLocalization()
    {
        $u = new User();
        $lan = $u->getUserLanguageToDisplay();
        $loc = Localization::getInstance();
        $loc->setLocale($lan);
    }
}
