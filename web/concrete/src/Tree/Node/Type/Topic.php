<?php

namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Loader;
use Core;

class Topic extends TreeNode
{
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\TopicTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\TopicTreeNodeAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'topic_tree_node';
    }

    public function getTreeNodeName()
    {
        return $this->treeNodeTopicName;
    }

    public function getTreeNodeTranslationContext()
    {
        return 'TopicName';
    }
    public function getTreeNodeDisplayName($format = 'html')
    {
        $name = Core::make('helper/text')->unhandle($this->getTreeNodeName());
        $name = tc($this->getTreeNodeTranslationContext(), $name);
        switch ($format) {
            case 'html':
                return h($name);
            case 'text':
            default:
                return $name;
        }
    }

    public function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from TreeTopicNodes where treeNodeID = ?', array($this->treeNodeID));
        $this->setPropertiesFromArray($row);
    }

    public function deleteDetails()
    {
        $db = Loader::db();
        $db->Execute('delete from TreeTopicNodes where treeNodeID = ?', array($this->treeNodeID));
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $obj->iconClass = 'fa fa-comment-o';

            return $obj;
        }
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->treeNodeTopicName, $parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public function setTreeNodeTopicName($treeNodeTopicName)
    {
        $db = Loader::db();
        $db->Replace('TreeTopicNodes',
            array('treeNodeID' => $this->getTreeNodeID(), 'treeNodeTopicName' => $treeNodeTopicName),
            array('treeNodeID'), true);
        $this->treeNodeTopicName = $treeNodeTopicName;
    }

    public function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add((string) $sx['name'], $parent);
    }

    public static function add($treeNodeTopicName, $parent = false)
    {
        $db = Loader::db();
        $node = parent::add($parent);
        $node->setTreeNodeTopicName($treeNodeTopicName);

        return $node;
    }

    /**
     * return @Concrete\Core\Tree\Node\Type\Topic | null
     */
    public static function getNodeByName($name)
    {
        $db = Loader::db();
        $treeNodeID = $db->GetOne('select treeNodeID from TreeTopicNodes where treeNodeTopicName = ?', array($name));
        if ($treeNodeID) {
            return static::getByID($treeNodeID);
        }
    }
}
