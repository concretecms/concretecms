<?php
namespace Concrete\Core\Foundation\Service;

use Concrete\Core\Application\Application;

class ProviderList
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
     * Loads and registers a class ServiceProvider class.
     *
     * @param  string $class
     */
    public function registerProvider($class)
    {
        $this->createInstance($class)->register();
    }

    /**
     * Creates an instance of the passed class string, override this to change how providers are instantiated.
     *
     * @param string $class The class name
     *
     * @return \Concrete\Core\Foundation\Service\Provider
     */
    protected function createInstance($class)
    {
        return new $class($this->app);
    }

    /**
     * Registers an array of service group classes.
     *
     * @param  array $groups
     */
    public function registerProviders($groups)
    {
        foreach ($groups as $group) {
            $this->registerProvider($group);
        }
    }

    /**
     * We are not allowed to serialize $this->app.
     */
    public function __sleep()
    {
        unset($this->app);
    }
}
