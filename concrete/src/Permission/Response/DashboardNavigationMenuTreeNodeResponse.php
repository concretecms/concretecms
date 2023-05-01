<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class DashboardNavigationMenuTreeNodeResponse extends Response
{
    protected function canAccessMenu()
    {
        $c = Page::getByPath('/dashboard/system/basics/menus/details');
        $cp = new Checker($c);
        return $cp->canViewPage();
    }

    public function canEditTreeNodePermissions()
    {
        return $this->canAccessMenu();
    }

    public function canViewTreeNode()
    {
        return $this->canAccessMenu();
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        return false;
    }

    public function canDeleteTreeNode()
    {
        return false;
    }

    public function canAddTreeSubNode()
    {
        return $this->canAccessTopics();
    }
}
