<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\Site\Service;

class ResolverFactory
{
    protected $application;
    protected $driver;

    public function __construct(Application $application, DriverInterface $driver)
    {
        $this->application = $application;
        $this->driver = $driver;
    }

    public function createResolver()
    {
        return new Resolver($this->application->make('site'), $this->driver);
    }

}
