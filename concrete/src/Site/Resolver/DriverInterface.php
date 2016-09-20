<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;
use Concrete\Core\Http\Request;

interface DriverInterface
{

    function getSite(Service $service, Request $request);
    function getActiveSiteForEditing(Service $service, Request $request);

}