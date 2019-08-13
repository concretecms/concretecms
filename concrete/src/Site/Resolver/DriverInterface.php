<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;
use Concrete\Core\Http\Request;

/**
 * @since 8.0.0
 */
interface DriverInterface
{

    function getSite(Service $service, Request $request);
    function getActiveSiteForEditing(Service $service, Request $request);

}