<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;

interface DriverInterface
{

    function getSite(Service $service);

}