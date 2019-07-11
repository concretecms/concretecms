<?php

namespace Concrete\Core\Routing;

class SystemRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setPrefix('/ccm/system/panels')->setNamespace('Concrete\Controller\Panel')
            ->routes('panels.php');

        $router->buildGroup()->routes('panels/details.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Frontend')->setPrefix('/ccm/assets/localization')
            ->routes('assets_localization.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/block')
            ->routes('actions/blocks.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/page')
            ->routes('actions/pages.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/user')
            ->routes('actions/users.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/user')
            ->routes('actions/users.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/file')
            ->routes('actions/files.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Conversation')->setPrefix('/ccm/system/dialogs/conversation')
            ->routes('dialogs/conversations.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Type')
            ->setPrefix('/ccm/system/dialogs/type')
            ->routes('dialogs/page_types.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\User')
            ->setPrefix('/ccm/system/dialogs/user')
            ->routes('dialogs/users.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Group')
            ->setPrefix('/ccm/system/dialogs/group')
            ->routes('dialogs/groups.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Page')
            ->setPrefix('/ccm/system/dialogs/page')
            ->routes('dialogs/pages.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Permissions')
            ->setPrefix('/ccm/system/dialogs/permissions')
            ->routes('dialogs/permissions.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\File')
            ->setPrefix('/ccm/system/dialogs/file')
            ->routes('dialogs/files.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Area')
            ->setPrefix('/ccm/system/dialogs/area')
            ->routes('dialogs/areas.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Block')
            ->setPrefix('/ccm/system/dialogs/block')
            ->routes('dialogs/blocks.php');

        $router->buildGroup()->setRequirements(['identifier' => '[A-Za-z0-9-_/.]+'])->routes('rss.php');

        $router->buildGroup()->routes('attributes.php');

        $router->buildGroup()->routes('search.php');

        $router->buildGroup()->routes('express.php');

        $router->buildGroup()->routes('marketplace.php');

        $router->buildGroup()->routes('permissions.php');

        $router->buildGroup()->routes('trees.php');

        $router->buildGroup()->routes('site.php');

        $router->buildGroup()->routes('calendar.php');

        $router->buildGroup()->routes('misc.php');
    }
}
