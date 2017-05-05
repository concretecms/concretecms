<?php
namespace Concrete\Core\Permission\Response;

use Page;
use Permissions;

class ExpressTreeNodeResponse extends TreeNodeResponse
{
    protected function canAccessEntryLocations()
    {
        $c = Page::getByPath('/dashboard/system/express/entries');
        $cp = new Permissions($c);

        return $cp->canViewPage();
    }

    public function canEditTreeNodePermissions()
    {
        return $this->canAccessEntryLocations();
    }

    public function canViewTreeNode()
    {
        return $this->canAccessEntryLocations();
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        return $this->canAccessEntryLocations();
    }

    public function canDeleteTreeNode()
    {
        return $this->canAccessEntryLocations()
            && $this->getPermissionObject()->getTreeNodeParentID() > 0;
    }

    public function canAddCategoryTreeNode()
    {
        return $this->canAccessEntryLocations();
    }

    public function canAddTreeSubNode()
    {
        return $this->canAccessEntryLocations();
    }

    public function canAddTopicTreeNode()
    {
        return $this->canAccessEntryLocations();
    }
}
