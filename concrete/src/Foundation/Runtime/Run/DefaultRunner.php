<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ServerInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\Url\Resolver\UrlResolverInterface;
use Concrete\Core\User\User;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default HTTP Runner.
 *
 * @todo Replace pipeline style functionality with middleware
 */
class DefaultRunner implements RunInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Repository */
    protected $config;

    /** @var UrlResolverInterface */
    protected $urlResolver;

    /** @var RouterInterface */
    protected $router;

    /** @var SiteService */
    protected $siteService;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ServerInterface */
    private $server;

    /**
     * DefaultRunner constructor.
     *
     * @param ServerInterface $server
     */
    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Begin the runtime.
     */
    public function run()
    {
        // Load in the /application/bootstrap/app.php file
        $this->loadBootstrap();

        $response = null;

        // Check if we're installed
        if ($this->app->isInstalled()) {
            // Call each step in the line
            // @todo Move these to individual middleware, this is basically a duplicated middleware pipeline
            $response = $this->trySteps([
                // Set the active language for the site, based either on the site locale, or the
                // current user record. This can be changed later as well, during runtime.
                // Start localization library.
                'setSystemLocale',

                // Set the system time zone (what should be the same as the database one)
                'initializeSystemTimezone',

                // Handle updating automatically
                'handleUpdates',

                // Set up packages first.
                // We do this because we don't want the entity manager to be loaded and we
                // want to give packages an opportunity to replace classes and load new classes
                'setupPackages',

                // Load site specific timezones. Has to come after packages because it
                // instantiates the site service, which sometimes packages need to override.
                'initializeSiteTimezone',

                // Define legacy urls, this may be the first thing that loads the entity manager
                'initializeLegacyUrlDefinitions',

                // Register legacy tools routes
                'registerLegacyRoutes',

                // Register legacy config values
                'registerLegacyConfigValues',

                // Handle loading permission keys
                'handlePermissionKeys',

                // Handle eventing
                'handleEventing',
            ]);
        } else {
            $this->initializeSystemTimezone();
        }

        // Create the request to use
        $request = $this->createRequest();

        if (!$response) {
            $response = $this->server->handleRequest($request);
        }

        // Prepare and return the response
        return $response->prepare($request);
    }

    /**
     * Define the base url if not defined
     * This will define `BASE_URL` to whatever is resolved from the resolver.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function initializeLegacyURLDefinitions()
    {
        if (!defined('BASE_URL')) {
            $resolver = $this->getUrlResolver();

            try {
                $url = rtrim((string) $resolver->resolve([]), '/');
                define('BASE_URL', $url);
            } catch (Exception $x) {
                return Response::create($x->getMessage(), 500);
            }
        }
    }

    protected function initializeSystemTimezone()
    {
        $config = $this->app->make('config');
        if (!$config->has('app.server_timezone')) {
            // There is no server timezone set.
            $config->set('app.server_timezone', @date_default_timezone_get());
        }
        @date_default_timezone_set($config->get('app.server_timezone'));
    }

    protected function initializeSiteTimezone()
    {
        $siteConfig = $this->app->make('site')->getSite()->getConfigRepository();

        if (!$siteConfig->has('timezone')) {
            // There is no timezone set.
            $siteConfig->set('timezone', @date_default_timezone_get());
        }
    }

    /**
     * @deprecated Splitted into initializeSystemTimezone and initializeSiteTimezone
     */
    protected function initializeTimezone()
    {
        $this->initializeSystemTimezone();
        $this->initializeSiteTimezone();
    }

    /**
     * Initialize localization.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     */
    protected function setSystemLocale()
    {
        $u = new User();
        $lan = $u->getUserLanguageToDisplay();
        $loc = Localization::getInstance();
        $loc->setContextLocale(Localization::CONTEXT_UI, $lan);
    }

    /**
     * Set legacy config values
     * This sets `concrete.site` to the current site's sitename.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function registerLegacyConfigValues()
    {
        $config = $this->getConfig();
        $name = $this->getSiteService()->getSite()->getSiteName();

        $config->set('concrete.site', $name);
    }

    /**
     * Register routes that power legacy functionality
     * This includes `/tools/tool_handle` and `/tools/blocks/block_handle/tool_handle`.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function registerLegacyRoutes()
    {
        $router = $this->getRouter();
        $router->register(
            '/tools/blocks/{btHandle}/{tool}',
            '\Concrete\Core\Legacy\Controller\ToolController::displayBlock',
            'blockTool',
            ['tool' => '[A-Za-z0-9_/.]+']
        );
        $router->register(
            '/tools/{tool}',
            '\Concrete\Core\Legacy\Controller\ToolController::display',
            'tool',
            ['tool' => '[A-Za-z0-9_/.]+']
        );
    }

    /**
     * Create the request object to use.
     */
    protected function createRequest()
    {
        $request = Request::createFromGlobals();
        $request::setInstance($request);

        return $request;
    }

    /**
     * Setup concrete5 packages.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function setupPackages()
    {
        $this->app->setupPackages();
    }

    /**
     * Load in the `/application/bootstrap/app.php` file.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function loadBootstrap()
    {
        // Set app so that the bootstrap file can access it
        $app = $this->app;
        include DIR_APPLICATION . '/bootstrap/app.php';
    }

    /**
     * Update automatically.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function handleUpdates()
    {
        $this->app->handleAutomaticUpdates();
    }

    /**
     * Fire HTTP events.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function handleEventing()
    {
        $this->getEventDispatcher()->dispatch('on_before_dispatch');
    }

    /**
     * Load all permission keys.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Response|void Returns a response if an error occurs
     */
    protected function handlePermissionKeys()
    {
        /* @todo Replace this with a testable service */
        Key::loadAll();
    }

    /**
     * Try a list of steps. If a response is returned, halt progression and return the response;.
     *
     * @param string[] $steps
     *
     * @return Response|null
     */
    protected function trySteps(array $steps)
    {
        foreach ($steps as $step) {
            // Run each step and return if there's a result
            if ($result = $this->$step()) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Get the config repository to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return Repository
     */
    protected function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->getDefaultConfig();
        }

        return $this->config;
    }

    /**
     * Get the default config repository to use.
     *
     * @return Repository
     */
    private function getDefaultConfig()
    {
        return $this->app->make('config');
    }

    /**
     * Set the config repository.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @param Repository $repository
     *
     * @return $this
     */
    public function setConfig(Repository $repository)
    {
        $this->config = $repository;

        return $this;
    }

    /**
     * Get the router to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return RouterInterface
     */
    protected function getRouter()
    {
        if (!$this->router) {
            $this->router = $this->getDefaultRouter();
        }

        return $this->router;
    }

    /**
     * Get the default router to use.
     *
     * @return RouterInterface
     */
    private function getDefaultRouter()
    {
        return $this->app->make(RouterInterface::class);
    }

    /**
     * Set the router.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @param RouterInterface $router
     *
     * @return $this
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Get the site service to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return SiteService
     */
    protected function getSiteService()
    {
        if (!$this->siteService) {
            $this->siteService = $this->getDefaultSiteService();
        }

        return $this->siteService;
    }

    /**
     * Get the default site service to use.
     *
     * @return SiteService
     */
    private function getDefaultSiteService()
    {
        return $this->app->make('site');
    }

    /**
     * Set the site service.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @param SiteService $site
     *
     * @return $this
     */
    public function setSiteService(SiteService $site)
    {
        $this->siteService = $site;

        return $this;
    }

    /**
     * Get the url resolver to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return UrlResolverInterface
     */
    protected function getUrlResolver()
    {
        if (!$this->urlResolver) {
            $this->urlResolver = $this->getDefaultUrlResolver();
        }

        return $this->urlResolver;
    }

    /**
     * Get the default url resolver to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return UrlResolverInterface
     */
    private function getDefaultUrlResolver()
    {
        return $this->app->make(CanonicalUrlResolver::class);
    }

    /**
     * Set the url resolver.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @param UrlResolverInterface $urlResolver
     *
     * @return $this
     */
    public function setUrlResolver(UrlResolverInterface $urlResolver)
    {
        $this->urlResolver = $urlResolver;

        return $this;
    }

    /**
     * Get the url resolver to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        if (!$this->eventDispatcher) {
            $this->eventDispatcher = $this->getDefaultEventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /**
     * Get the default url resolver to use.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @return EventDispatcherInterface
     */
    private function getDefaultEventDispatcher()
    {
        return $this->app->make('director');
    }

    /**
     * Set the url resolver.
     *
     * @deprecated In a future major version this will be part of HTTP middleware
     *
     * @param EventDispatcherInterface $urlResolver
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $urlResolver)
    {
        $this->eventDispatcher = $urlResolver;

        return $this;
    }
}
