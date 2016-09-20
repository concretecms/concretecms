<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;
use Concrete\Core\Http\Request;

class Resolver
{

    protected $driver;
    protected $service;
    protected $request;

    public function __construct(Service $service, DriverInterface $driver, Request $request)
    {
        $this->driver = $driver;
        $this->service = $service;
        $this->request = $request;
    }

    public function getSite()
    {
        return $this->driver->getSite($this->service, $this->request);
    }

}
