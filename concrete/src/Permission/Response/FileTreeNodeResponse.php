<?php
namespace Concrete\Core\Permission\Response;

use TaskPermission;

class FileTreeNodeResponse extends TreeNodeResponse
{

    protected function getPermissionsCheckerObject()
    {
        $f = $this->getPermissionObject()->getTreeNodeFileObject();
        if (is_object($f)) {
            $fp = new \Permissions($f);
            return $fp;
        }
    }

    public function canEditTreeNodePermissions()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->validate('edit_file_permissions');
        }
    }

    public function canViewTreeNode()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->canViewFileInFileManager();
        }
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->validate('edit_file_properties');
        }
    }

    public function canAddTreeSubNode()
    {
        return false;
    }

    public function canDeleteTreeNode()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->validate('delete_file');
        }
    }
}
