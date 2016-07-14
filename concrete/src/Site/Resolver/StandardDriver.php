<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Factory;
use Concrete\Core\Site\Service;

class StandardDriver implements DriverInterface
{

    public function getCurrentSite(Service $service)
    {
        return $service->getDefault();
    }


}