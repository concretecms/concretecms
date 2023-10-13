<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class DashboardPackagePagesTreeNodeResponse extends Response
{
    protected function canAccessDashboard()
    {
        $c = Page::getByPath('/dashboard');
        $cp = new Checker($c);
        return $cp->canViewPage();
    }

    public function canEditTreeNodePermissions()
    {
        return $this->canAccessDashboard();
    }

    public function canViewTreeNode()
    {
        return $this->canAccessDashboard();
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
        return false;
    }

}
