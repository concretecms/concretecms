<?php

namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Loader;

abstract class Category extends TreeNode
{
    public function getTreeNodeName()
    {
        return $this->treeNodeCategoryName;
    }

    public function getTreeNodeTranslationContext()
    {
        return 'TreeNodeCategoryName';
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->getTreeNodeName()) {
            $name = tc($this->getTreeNodeTranslationContext(), $this->getTreeNodeName());
            switch ($format) {
                case 'html':
                    return h($name);
                case 'text':
                default:
                    return $name;
            }
        } elseif ($this->treeNodeParentID == 0) {
            return t('Categories');
        }
    }

    public function loadDetails()
    {
        $db = Loader::db();
        $r = $db->GetRow('select * from TreeCategoryNodes where treeNodeID = ?', array($this->treeNodeID));
        $this->setPropertiesFromArray($r);
    }

    public function deleteDetails()
    {
        $db = Loader::db();
        $db->Execute('delete from TreeCategoryNodes where treeNodeID = ?', array($this->treeNodeID));
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->treeNodeCategoryName, $parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $obj->isFolder = true;

            return $obj;
        }
    }

    public function setTreeNodeCategoryName($treeNodeCategoryName)
    {
        $db = Loader::db();
        $db->Replace('TreeCategoryNodes', array('treeNodeID' => $this->getTreeNodeID(), 'treeNodeCategoryName' => $treeNodeCategoryName), array('treeNodeID'), true);
        $this->treeNodeCategoryName = $treeNodeCategoryName;
    }

    public static function add($treeNodeCategoryName = '', $parent = false)
    {
        $node = parent::add($parent);
        $node->setTreeNodeCategoryName($treeNodeCategoryName);

        return $node;
    }

    public static function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add((string) $sx['name'], $parent);
    }
}
