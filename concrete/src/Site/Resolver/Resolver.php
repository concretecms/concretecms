<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;

class Resolver
{

    protected $driver;
    protected $service;

    public function __construct(Service $service, DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->service = $service;
    }

    public function getCurrentSite()
    {
        return $this->driver->getCurrentSite($this->service);
    }

}
