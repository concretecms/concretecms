<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Command\UpdateDashboardMenuCommand;
use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Menu\Command\AddMenuCommand;
use Concrete\Core\Menu\Type\Manager;
use Concrete\Core\Page\Page;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\DashboardPackagePages;
use Concrete\Core\Tree\Node\Type\Page as PageNode;

class CreateBackendNavigationRoutineHandler
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function createMenu(): Menu
    {
        $type = $this->app->make(Manager::class)->driver('dashboard');
        $command = new AddMenuCommand();
        $command->setType($type);
        $command->setName('Dashboard Menu');
        return $this->app->executeCommand($command);
    }

    protected function addItem(Node $node, string $path, $includeSubpagesInMenu = true): PageNode
    {
        $page = Page::getByPath($path, 'RECENT');
        return PageNode::add($page, $includeSubpagesInMenu, $node);
    }

    protected function addDashboardPackagePagesNode(Node $node)
    {
        return DashboardPackagePages::add($node);
    }

    public function __invoke()
    {
        $menu = $this->createMenu();
        $tree = $menu->getTree();
        $node = $tree->getRootTreeNodeObject();

        $this->addItem($node, '/dashboard/welcome');
        $this->addItem($node, '/dashboard/sitemap');
        $this->addItem($node, '/dashboard/files');
        $this->addItem($node, '/dashboard/users');
        $this->addItem($node, '/dashboard/express');
        $this->addItem($node, '/dashboard/boards');
        $this->addItem($node, '/dashboard/reports');
        $this->addItem($node, '/dashboard/pages');
        $this->addItem($node, '/dashboard/calendar');
        $this->addItem($node, '/dashboard/conversations');
        $this->addItem($node, '/dashboard/blocks');
        $this->addItem($node, '/dashboard/extend');
        $systemNode = $this->addItem($node, '/dashboard/system', false);

        $this->addDashboardPackagePagesNode($node);

        $this->addItem($systemNode, '/dashboard/system/basics');
        $this->addItem($systemNode, '/dashboard/system/express');
        $this->addItem($systemNode, '/dashboard/system/multisite');
        $this->addItem($systemNode, '/dashboard/system/multilingual');
        $this->addItem($systemNode, '/dashboard/system/seo');
        $this->addItem($systemNode, '/dashboard/system/files');
        $this->addItem($systemNode, '/dashboard/system/automation');
        $this->addItem($systemNode, '/dashboard/system/notification');
        $this->addItem($systemNode, '/dashboard/system/optimization');
        $this->addItem($systemNode, '/dashboard/system/permissions');
        $this->addItem($systemNode, '/dashboard/system/registration');
        $this->addItem($systemNode, '/dashboard/system/mail');
        $this->addItem($systemNode, '/dashboard/system/calendar');
        $this->addItem($systemNode, '/dashboard/system/boards');
        $this->addItem($systemNode, '/dashboard/system/conversations');
        $this->addItem($systemNode, '/dashboard/system/attributes');
        $this->addItem($systemNode, '/dashboard/system/environment');
        $this->addItem($systemNode, '/dashboard/system/update');
        $this->addItem($systemNode, '/dashboard/system/api');

        $command = new UpdateDashboardMenuCommand($menu->getId());
        $this->app->executeCommand($command);
    }


}
