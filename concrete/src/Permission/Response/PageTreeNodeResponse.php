<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Permission\Checker;

class PageTreeNodeResponse extends TreeNodeResponse
{

    protected function getPermissionsCheckerObject()
    {
        $c = $this->getPermissionObject()->getTreeNodePageObject();
        if (is_object($c)) {
            $cp = new Checker($c);
            return $cp;
        }
    }

    public function canEditTreeNodePermissions()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->validate('edit_page_permissions');
        }
    }

    public function canViewTreeNode()
    {
        $checker = $this->getPermissionsCheckerObject();
        if (is_object($checker)) {
            return $checker->canViewPageInSitemap();
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
            return $checker->validate('edit_page_properties');
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
            return $checker->validate('delete_page');
        }
    }
}
