<?php

namespace Concrete\Core\Foundation\Runtime\Boot;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\File\Type\TypeList;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Http\Request;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Route;
use Illuminate\Config\Repository;
use Symfony\Component\HttpFoundation\Request as SymphonyRequest;
use Symfony\Component\HttpFoundation\Response;

class DefaultBooter implements BootInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

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
         * Set configured error reporting
         * ----------------------------------------------------------------------------
         */
        $this->setupErrorReporting($config);

        /*
         * ----------------------------------------------------------------------------
         * Enable Localization
         * ----------------------------------------------------------------------------
         */
        $this->initializeLocalization($app);

        /*
         * ----------------------------------------------------------------------------
         * Finalize paths.
         * ----------------------------------------------------------------------------
         */
        require DIR_BASE_CORE . '/bootstrap/paths_configured.php';

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
         * Simple legacy constants like APP_CHARSET
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
     * Setup the configured error reporting.
     *
     * @param Repository $config
     */
    private function setupErrorReporting(Repository $config)
    {
        $error_reporting = $config->get('concrete.debug.error_reporting');
        if ((string) $error_reporting !== '') {
            error_reporting((int) $error_reporting);
        }
    }

    /**
     * Enable localization.
     *
     * This needs to happen very early in the boot process because the
     * application configuration (config/app.php) is already calling the t()
     * functions which are initializing the Localization singleton. When the
     * singleton is being initialized, these services need to be already
     * available.
     *
     * @param Application $app
     */
    private function initializeLocalization(Application $app)
    {
        $localization_provider = $app->make('Concrete\Core\Localization\LocalizationEssentialServiceProvider');
        $localization_provider->register();
    }

    /**
     * @param Application $app
     */
    private function initializeEnvironmentDetection(Application $app)
    {
        $db_config = [];
        if (file_exists(DIR_CONFIG_SITE . '/database.php')) {
            $db_config = include DIR_CONFIG_SITE . '/database.php';
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
        if (version_compare(PHP_VERSION, '7.2.0alpha1') < 0) {
            $list->register('Concrete\Core\Foundation\Object', 'Concrete\Core\Foundation\ConcreteObject');
        }

        return $list;
    }

    /**
     * @param Application $app
     * @param Repository $config
     */
    private function initializeServiceProviders(Application $app, Repository $config)
    {
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
        define('DIR_REL', $app['app_relative_path']);
    }

    /**
     * @param Repository $config
     */
    private function initializeAssets(Repository $config)
    {
        $asset_list = AssetList::getInstance();

        $asset_list->registerMultiple($config->get('app.assets', []));
        $asset_list->registerGroupMultiple($config->get('app.asset_groups', []));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     */
    private function initializeRoutes(Repository $config)
    {
        Route::registerMultiple($config->get('app.routes'));
        Route::setThemesByRoutes($config->get('app.theme_paths', []));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     */
    private function initializeFileTypes(Repository $config)
    {
        $type_list = TypeList::getInstance();
        $type_list->defineMultiple($config->get('app.file_types', []));
        $type_list->defineImporterAttributeMultiple($config->get('app.importer_attributes', []));
    }

    /**
     * @param \Illuminate\Config\Repository $config
     *
     * @return Request
     */
    private function initializeRequest(Repository $config)
    {
        /*
         * Patch the request, so that it can be seen as if it was an ajax call.
         * This can only be done by patching the superglobals, because we have
         * to consider 3rd party libraries (like Symfony for instance) which use
         * those superglobals.
         */
        if (isset($_POST['__ccm_consider_request_as_xhr']) && $_POST['__ccm_consider_request_as_xhr'] === '1') {
            unset($_POST['__ccm_consider_request_as_xhr']);
            unset($_REQUEST['__ccm_consider_request_as_xhr']);
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }

        /*
         * ----------------------------------------------------------------------------
         * Set trusted proxies and headers for the request
         * ----------------------------------------------------------------------------
         */
        $trustedProxiesIps = $config->get('concrete.security.trusted_proxies.ips');
        if ($trustedProxiesIps) {
            $proxyHeaders = $config->get('concrete.security.trusted_proxies.headers');
            if (defined(SymphonyRequest::class . '::HEADER_X_FORWARDED_ALL')) {
                // Symphony 3.3+
                if (is_array($proxyHeaders)) {
                    $proxyHeadersBitfield = 0;
                    $legacyValues = [
                        'forwarded' => Request::HEADER_FORWARDED,
                        'client_ip' => Request::HEADER_X_FORWARDED_FOR,
                        'client_host' => Request::HEADER_X_FORWARDED_HOST,
                        'client_proto' => Request::HEADER_X_FORWARDED_PROTO,
                        'client_port' => Request::HEADER_X_FORWARDED_PORT,
                    ];
                    foreach ($proxyHeaders as $proxyHeader) {
                        if (isset($legacyValues[$proxyHeader])) {
                            $proxyHeadersBitfield |= $legacyValues[$proxyHeader];
                        }
                    }
                } else {
                    $proxyHeadersBitfield = (int) $proxyHeaders;
                }
                if ($proxyHeadersBitfield === 0) {
                    $proxyHeadersBitfield = -1;
                }
                Request::setTrustedProxies($trustedProxiesIps, $proxyHeadersBitfield);
            } else {
                // Symphony 3.2-
                if (is_array($proxyHeaders)) {
                    foreach ($proxyHeaders as $key => $value) {
                        Request::setTrustedHeaderName($key, $value);
                    }
                }
                Request::setTrustedProxies($trustedProxiesIps);
            }
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
            if (
                !$request->matches('/install/*')
                && $request->getPath() != '/install'
                && !$request->matches('/ccm/assets/localization/*')
            ) {
                $manager = $app->make('Concrete\Core\Url\Resolver\Manager\ResolverManager');
                $response = new RedirectResponse($manager->resolve(['install']));

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
}
