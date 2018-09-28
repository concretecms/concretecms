<?php
namespace Concrete\Core\Foundation\Service;

use Concrete\Core\Application\Application;

/**
 *  Extending this class allows groups of services to be registered at once.
 */
abstract class Provider
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Registers the services provided by this provider.
     */
    abstract public function register();

    public function provides()
    {
        return [];
    }
}
