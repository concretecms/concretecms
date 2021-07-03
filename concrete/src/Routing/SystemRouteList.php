<?php

namespace Concrete\Core\Routing;

class SystemRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setPrefix('/ccm/system/panels')->setNamespace('Concrete\Controller\Panel')
            ->routes('panels.php')
        ;

        $router->buildGroup()->routes('panels/details.php');

        $router->buildGroup()->setNamespace('Concrete\Controller\Frontend')->setPrefix('/ccm/assets/localization')
            ->routes('assets_localization.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/block')
            ->routes('actions/blocks.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/page')
            ->routes('actions/pages.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/user')
            ->routes('actions/users.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/group')
            ->routes('actions/groups.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend')->setPrefix('/ccm/system/file')
            ->routes('actions/files.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Backend\Board')->setPrefix('/ccm/system/board')
            ->routes('actions/boards.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Conversation')->setPrefix('/ccm/system/dialogs/conversation')
            ->routes('dialogs/conversations.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Type')
            ->setPrefix('/ccm/system/dialogs/type')
            ->routes('dialogs/page_types.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\User')
            ->setPrefix('/ccm/system/dialogs/user')
            ->routes('dialogs/users.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Groups')
            ->setPrefix('/ccm/system/dialogs/groups')
            ->routes('dialogs/groups.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Page')
            ->setPrefix('/ccm/system/dialogs/page')
            ->routes('dialogs/pages.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Logs')
            ->setPrefix('/ccm/system/dialogs/logs')
            ->routes('dialogs/logs.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Permissions')
            ->setPrefix('/ccm/system/dialogs/permissions')
            ->routes('dialogs/permissions.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\File')
            ->setPrefix('/ccm/system/dialogs/file')
            ->routes('dialogs/files.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Area')
            ->setPrefix('/ccm/system/dialogs/area')
            ->routes('dialogs/areas.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Block')
            ->setPrefix('/ccm/system/dialogs/block')
            ->routes('dialogs/blocks.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\SiteType')
            ->setPrefix('/ccm/system/dialogs/site_type')
            ->routes('dialogs/site_types.php')
        ;

        $router->buildGroup()->setRequirements(['identifier' => '[A-Za-z0-9-_/.]+'])->routes('rss.php');

        $router->buildGroup()->routes('attributes.php');

        $router->buildGroup()
            ->routes('search.php')
        ;

        $router->buildGroup()->routes('express.php');

        $router->buildGroup()->routes('marketplace.php');

        $router->buildGroup()->routes('permissions.php');

        $router->buildGroup()->routes('trees.php');

        $router->buildGroup()->routes('site.php');

        $router->buildGroup()->routes('boards.php');

        $router->buildGroup()->routes('calendar.php');

        $router->buildGroup()->routes('misc.php');

        $router->buildGroup()
            ->setNamespace('Concrete\Controller\Backend\Dashboard')
            ->setPrefix('/ccm/system/backend/dashboard')
            ->routes('backend/dashboard.php')
        ;

        $router->buildGroup()
            ->setNamespace('Concrete\Controller\Backend\Page\Type')
            ->setPrefix('/ccm/system/page/type')
            ->routes('backend/page_types.php')
        ;   

        $router->buildGroup()->setNamespace('Concrete\Controller\Dialog\Workflow')
            ->setPrefix('/ccm/system/dialogs/workflow')
            ->routes('dialogs/workflows.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Workflow')
            ->setPrefix('/ccm/system/workflow')
            ->routes('workflow.php')
        ;

        $router->buildGroup()->setNamespace('Concrete\Controller\Frontend\Conversations')
            ->setPrefix('/ccm/frontend/conversations')
            ->routes('conversations.php')
        ;
    }
}
