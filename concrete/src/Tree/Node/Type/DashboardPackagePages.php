<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Navigation\Item\DashboardPageItem;
use Concrete\Core\Navigation\Item\DividerItem;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Navigation\Navigation;
use Concrete\Core\Navigation\NavigationInterface;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Assignment\TreeNodeAssignment;
use Concrete\Core\Permission\Response\DashboardPackagePagesTreeNodeResponse;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Menu\DashboardPackagePagesMenu;

class DashboardPackagePages extends Node implements NavigationMenuNodeGroupInterface
{

    public function getPermissionResponseClassName()
    {
        return DashboardPackagePagesTreeNodeResponse::class;
    }

    public function getPermissionAssignmentClassName()
    {
        return TreeNodeAssignment::class;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'dashboard_package_pages_tree_node';
    }

    public function getTreeNodeTypeName()
    {
        return 'DashboardPackagesPages';
    }

    public function getTreeNodeMenu()
    {
        return new DashboardPackagePagesMenu($this);
    }


    public function getTreeNodeName()
    {
        return 'Dashboard Packages Pages';
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($format === 'html') {
            return h($this->getTreeNodeDisplayName('text'));
        }

        return $this->getTreeNodeName();
    }

    public function loadDetails()
    {
        return false;
    }

    public function deleteDetails()
    {
        return false;
    }

    public function getNavigation(): NavigationInterface
    {
        $page = CorePage::getByPath('/dashboard');
        $children = $page->getCollectionChildren();
        $navigation = new Navigation();
        $packagePages = [];
        foreach ($children as $child) {
            if ($child->getPackageID() > 0) {
                $packagePages[] = $child;
            }
        }
        if (count($packagePages)) {
            $navigation->add(new DividerItem());
            foreach ($packagePages as $packagePage) {
                $navigation->add(new DashboardPageItem($packagePage));
            }
        }
        return $navigation;
    }
}
