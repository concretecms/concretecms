<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\Site\Service;

class ResolverFactory
{
    protected $application;
    protected $driver;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function createResolver(Service $service)
    {
        return $this->application->make('Concrete\Core\Site\Resolver\Resolver', ['service' => $service]);
    }

}
