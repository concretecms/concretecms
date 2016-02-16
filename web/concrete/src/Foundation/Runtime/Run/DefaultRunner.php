<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Events;

class DefaultRunner implements RunInterface, ApplicationAwareInterface
{
    /** @var Application */
    protected $app;

    /** @var Repository */
    protected $config;

    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Begin the runtime.
     */
    public function run()
    {
        $app = $this->app;
        $request = $this->request;

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

        /*
         * ----------------------------------------------------------------------------
         * Get the response to the current request
         * ----------------------------------------------------------------------------
         */
        return $app->dispatch($request);
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
}
