<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Factory;
use Concrete\Core\Site\Service;
use Concrete\Core\Http\Request;

class StandardDriver implements DriverInterface
{

    public function getSite(Service $service, Request $request)
    {
        return $service->getDefault();
    }

    public function getActiveSiteForEditing(Service $service, Request $request)
    {
        return $service->getDefault();
    }

}