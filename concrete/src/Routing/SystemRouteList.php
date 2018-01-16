<?php
namespace Concrete\Core\Routing;

class SystemRouteList implements RouteListInterface
{

    public function loadRoutes(Router $router)
    {
        $router->buildGroup()
            ->setPrefix('/ccm/system/panels')
            ->setNamespace('Concrete\Controller\Panel')
            ->routes('panels.php');
    }


}
