<?php
namespace Concrete\Core\Foundation\Service;

use \Concrete\Core\Application\Application;
use Closure;

/**
 *  Extending this class allows groups of services to be registered at once.
 */
abstract class Provider
{

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    abstract public function register();

}
