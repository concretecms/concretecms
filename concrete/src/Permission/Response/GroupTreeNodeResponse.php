<?php
namespace Concrete\Core\Permission\Response;

use TaskPermission;

class GroupTreeNodeResponse extends TreeNodeResponse
{
    public function canEditTreeNodePermissions()
    {
        return $this->validate('edit_group_permissions');
    }

    public function canViewTreeNode()
    {
        $tp = new TaskPermission();
        return $tp->canAccessGroupSearch();
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        return $this->validate('edit_group');
    }

    public function canAddTreeSubNode()
    {
        return $this->validate('add_sub_group');
    }

    public function canDeleteTreeNode()
    {
        return false;
    }
}
