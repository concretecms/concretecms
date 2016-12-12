<?php
namespace Concrete\Core\Tree\Type;

use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Node\Type\Category as CategoryTreeNode;
use Database;
use Group as UserGroup;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Key\CategoryTreeNodeKey as CategoryTreeNodePermissionKey;
use Concrete\Core\Permission\Access\Access as PermissionAccess;

class Topic extends Tree
{
    /** Returns the standard name for this tree
     * @return string
     */
    public function getTreeName()
    {
        return $this->topicTreeName;
    }

    /** Returns the display name for this tree (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTreeDisplayName($format = 'html')
    {
        $value = tc('TreeName', $this->topicTreeName);
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public static function getDefault()
    {
        $db = Database::connection();
        $treeID = $db->GetOne('select treeID from TopicTrees order by treeID asc');

        return Tree::getByID($treeID);
    }

    protected function deleteDetails()
    {
        $db = Database::connection();
        $db->Execute('delete from TopicTrees where treeID = ?', array($this->treeID));
    }

    public static function getByName($name)
    {
        $db = Database::connection();
        $treeID = $db->GetOne('select treeID from TopicTrees where topicTreeName = ?', array($name));

        return Tree::getByID($treeID);
    }

    public static function add($name)
    {
        // copy permissions from the other node.
        $rootNode = CategoryTreeNode::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);
        $tree->setTopicTreeName($name);

        // by default, topic trees are viewable by all
        $guestGroupEntity = GroupPermissionAccessEntity::getOrCreate(UserGroup::getByID(GUEST_GROUP_ID));
        $pk = CategoryTreeNodePermissionKey::getByHandle('view_category_tree_node');
        if (is_object($pk)) {
            $pk->setPermissionObject($rootNode);
            $pa = PermissionAccess::create($pk);
            $pa->addListItem($guestGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
        return $tree;
    }

    public function exportDetails(\SimpleXMLElement $sx)
    {
        $default = self::getDefault();
        if (is_object($default) && $default->getTreeID() == $this->getTreeID()) {
            $sx->addAttribute('default', 1);
        }
    }

    public static function importDetails(\SimpleXMLElement $sx)
    {
        $isDefault = (string) $sx['default'];
        if ($isDefault) {
            return static::getDefault();
        } else {
            $name = (string) $sx['name'];
            $tree = static::getByName($name);
            if (is_object($tree)) {
                // We already have a tree. But we know we're going to have sub-nodes of this tree in the import, so let's keep the same
                // tree so that pointers to attributes work, but let's clear it out.
                $root = $tree->getRootTreeNodeObject();
                $root->populateChildren();
                $children = $root->getChildNodes();
                foreach ($children as $child) {
                    $child->delete();
                }

                return static::getByName($name);
            } else {
                return static::add($name);
            }
        }
    }

    protected function loadDetails()
    {
        $db = Database::connection();
        $row = $db->GetRow('select treeID, topicTreeName from TopicTrees where treeID = ?', array($this->treeID));
        if (!empty($row)) {
            $this->setPropertiesFromArray($row);

            return $this;
        }
    }

    public function setTopicTreeName($name)
    {
        $db = Database::connection();
        $db->Replace('TopicTrees', array('treeID' => $this->getTreeID(), 'topicTreeName' => $name), array('treeID'), true);
        $this->topicTreeName = $name;
    }

    public static function getList()
    {
        $db = Database::connection();
        $treeIDs = $db->GetCol('select TopicTrees.treeID from TopicTrees inner join Trees on TopicTrees.treeID = Trees.treeID order by treeDateAdded asc');
        $trees = array();
        foreach ($treeIDs as $treeID) {
            $tree = self::getByID($treeID);
            if (is_object($tree)) {
                $trees[] = $tree;
            }
        }

        return $trees;
    }
}
