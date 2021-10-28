<?php

namespace Concrete\Core\Permission\Response;

class GroupFolderResponse extends TreeNodeResponse
{

    public function canViewTreeNode()
    {
        return $this->validate('search_group_folder');
    }

    public function canDeleteTreeNode()
    {
        return $this->validate('delete_group_folder');
    }

    public function canAddTreeSubNode()
    {
        return $this->validate('add_group');
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        return $this->validate('edit_group_folder');
    }

    public function canEditTreeNodePermissions()
    {
        return $this->validate('edit_group_folder_permissions');
    }

    public function canAddFiles()
    {
        return $this->validate('add_group');
    }
}
