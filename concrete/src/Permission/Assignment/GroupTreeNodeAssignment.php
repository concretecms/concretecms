<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Group;

class GroupTreeNodeAssignment extends TreeNodeAssignment
{

    protected $permissionObjectToCheck;

    /**
     * @param Group $groupTreeNode
     */
    public function setPermissionObject($groupTreeNode)
    {
        $this->permissionObject = $groupTreeNode;

        if ($groupTreeNode->overrideParentTreeNodePermissions()) {
            $this->permissionObjectToCheck = $groupTreeNode;
        } else {
            $this->permissionObjectToCheck = Node::getByID($groupTreeNode->getTreeNodePermissionsNodeID());
        }
    }


    protected $inheritedPermissions = array(
        'search_users_in_group' => 'search_group_folder',
        'edit_group' => 'edit_group_folder',
        'assign_group' => 'assign_groups',
        'add_sub_group' => 'add_group',
        'edit_group_permissions' => 'edit_group_folder_permissions',
    );

    public function getPermissionAccessObject()
    {
        $db = Database::connection();
        $r = null;
        if ($this->permissionObjectToCheck instanceof Group) {
            $r = $db->GetOne('select paID from TreeNodePermissionAssignments where treeNodeID = ? and pkID = ?', array(
                $this->permissionObject->getTreeNodePermissionsNodeID(), $this->pk->getPermissionKeyID(),
            ));
        } else if (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
            $r = $db->GetOne(
                'select paID from TreeNodePermissionAssignments where treeNodeID = ? and pkID = ?',
                array(
                    $this->permissionObjectToCheck->getTreeNodePermissionsNodeID(),
                    $inheritedPKID,
                )
            );
        }

        $pa = null;
        if ($r) {
            $pa = Access::getByID($r, $this->pk, false);
        }

        return $pa;

    }
}
