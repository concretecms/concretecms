<?php
namespace Concrete\Core\Application;

/**
 * Trait ApplicationAwareTrait
 * A trait used with ApplicationAwareInterface.
 */
trait ApplicationAwareTrait
{
    /**
     * The Application instance.
     *
     * @var Application
     */
    protected $app = null;

    /**
     * Setter method for the application.
     *
     * @param Application $app
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Getter method for the application.
     *
     * @return Application $app
     */
    public function getApplication()
    {
        if ($this->app === null) {
            $this->app = \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
        }

        return $this->app;
    }
}
