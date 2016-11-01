<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Menu\TopicMenu;
use Loader;

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

    public function getTreeNodeTranslationContext()
    {
        return 'TopicName';
    }

    public function getTreeNodeTypeName()
    {
        return 'Topic';
    }

    public function getTreeNodeMenu()
    {
        return new TopicMenu($this);
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        $name = $this->getTreeNodeName();
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
        return false;
    }

    public function deleteDetails()
    {
        return false;
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $p = new \Permissions($this);
            $obj->canAddTopicTreeNode = $p->canAddTopicTreeNode();
            $obj->icon = 'fa fa-file-o';
            return $obj;
        }
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->treeNodeName, $parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public static function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add((string) $sx['name'], $parent);
    }

    public static function add($treeNodeTopicName = '', $parent = false)
    {
        $db = Loader::db();
        $node = parent::add($parent);
        $node->setTreeNodeName($treeNodeTopicName);
        return $node;
    }

}
