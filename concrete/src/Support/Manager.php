<?php
namespace Concrete\Core\Support;

class Manager extends \Illuminate\Support\Manager
{

    protected $defaultDriver;

    protected function createDriver($driver)
    {

        // Note –this overrides the laravel createDriver because we do concrete5
        // camelcasing magic.

        $method = 'create'.camelcase($driver).'Driver';

        // We'll check to see if a creator method exists for the given driver. If not we
        // will check for a custom driver creator, which allows developers to create
        // drivers using their own customized driver creator Closure to create it.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        if ($this->defaultDriver) {
            return $this->defaultDriver;
        }

        return null;
    }

}
