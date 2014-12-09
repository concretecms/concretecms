<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Loader;
use Group as UserGroup;

class Group extends TreeNode
{
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\GroupTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\GroupTreeNodeAssignment';
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'group_tree_node';
    }

    public function getTreeNodeGroupID()
    {
        return $this->gID;
    }
    public function getTreeNodeGroupObject()
    {
        return UserGroup::getByID($this->gID);
    }
    public function getTreeNodeName()
    {
        $g = UserGroup::getByID($this->gID);
        if (is_object($g)) {
            return $g->getGroupName();
        }
    }
    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->treeNodeParentID == 0) {
            return t('All Groups');
        }

        $g = UserGroup::getByID($this->gID);
        if (is_object($g)) {
            $gName = $g->getGroupDisplayName(false);
            switch ($format) {
                case 'html':
                    return h($gName);
                case 'text':
                default:
                    return $gName;
            }
        }
    }

    public function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from TreeGroupNodes where treeNodeID = ?', array($this->treeNodeID));
        $this->setPropertiesFromArray($row);
    }

    public function move(TreeNode $newParent)
    {
        parent::move($newParent);
        $g = $this->getTreeNodeGroupObject();
        if (is_object($g)) {
            $g->rescanGroupPathRecursive();
        }
    }

    public static function getTreeNodeByGroupID($gID)
    {
        $db = Loader::db();
        $treeNodeID = $db->GetOne('select treeNodeID from TreeGroupNodes where gID = ?', array($gID));
        if ($treeNodeID) {
            $tn = TreeNode::getByID($treeNodeID);

            return $tn;
        }
    }

    public function deleteDetails()
    {
        $db = Loader::db();
        $db->Execute('delete from TreeGroupNodes where treeNodeID = ?', array($this->treeNodeID));
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $obj->gID = $this->gID;
            $obj->iconClass = 'fa fa-users';

            return $obj;
        }
    }

    public function setTreeNodeGroup(UserGroup $g)
    {
        $db = Loader::db();
        $db->Replace('TreeGroupNodes', array('treeNodeID' => $this->getTreeNodeID(), 'gID' => $g->getGroupID()), array('treeNodeID'), true);
        $this->gID = $g->getGroupID();
    }

    public static function add($group = false, $parent = false)
    {
        $db = Loader::db();
        $node = parent::add($parent);
        if (is_object($group)) {
            $node->setTreeNodeGroup($group);
        }

        return $node;
    }

}
