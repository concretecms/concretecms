<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Server;
use Concrete\Core\Http\ServerInterface;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Events;

class DefaultRunner implements RunInterface, ApplicationAwareInterface
{
    /** @var Application */
    protected $app;

    /** @var Repository */
    protected $config;

    /**
     * @var \Concrete\Core\Http\ServerInterface
     */
    private $server;

    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Begin the runtime.
     */
    public function run()
    {
        $app = $this->app;

        include DIR_APPLICATION . '/bootstrap/app.php';

        if ($this->app->isInstalled()) {
            /*
             * ----------------------------------------------------------------------------
             * Now that we have languages out of the way, we can run our package on_start
             * methods
             * ----------------------------------------------------------------------------
             */
            $app->setupPackages();

            /*
             * ----------------------------------------------------------------------------
             * Legacy Definitions. This has to come after packages because this
             * essentially loads the entity manager, and the entity manager loads classes
             * found in its config, which may be classes that haven't been autoloaded by initialPackages. It also
             * has to come after setupPackages() in case an autoloader is configured in on_start()
             * ----------------------------------------------------------------------------
             */
            $this->initializeLegacyURLDefinitions($app);


            /*
             * Handle automatic updating. Must come after setupPackages() because some things setup autoloaders in on_start() of their package
             * controller
             */
            $app->handleAutomaticUpdates();


            // This is a crappy place for this, but it has to come AFTER the packages because sometimes packages
            // want to replace legacy "tools" URLs with the new MVC, and the tools paths are so greedy they don't
            // work unless they come at the end.
            $this->registerLegacyRoutes();

            /* ----------------------------------------------------------------------------
             * Register legacy routes
             * ----------------------------------------------------------------------------
             */
            $this->registerLegacyRoutes();

            /*
             * ----------------------------------------------------------------------------
             * Load all permission keys into our local cache.
             * ----------------------------------------------------------------------------
             */
            Key::loadAll();
        }

        /*
         * ----------------------------------------------------------------------------
         * Fire an event for intercepting the dispatch
         * ----------------------------------------------------------------------------
         */
        Events::dispatch('on_before_dispatch');

        $request = Request::createFromGlobals();
        return $this->server->handleRequest($request);
    }

    /**
     * @param Repository $config
     * @param Application $app
     */
    private function initializeLegacyURLDefinitions(Application $app)
    {
        if (!defined('BASE_URL')) {
            try {
                define('BASE_URL', rtrim((string) $app->make('url/canonical'), '/'));
            } catch (\Exception $x) {
                echo $x->getMessage();
                die(1);
            }
        }
    }


    /**
     * Set the application object.
     *
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->app = $application;
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

}
