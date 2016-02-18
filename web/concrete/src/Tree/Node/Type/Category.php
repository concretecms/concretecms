<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Loader;

abstract class Category extends TreeNode
{
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
        return false;
    }

    public function deleteDetails()
    {
        return false;
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->treeNodeName, $parent);
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

    public static function add($treeNodeCategoryName = '', $parent = false)
    {
        $node = parent::add($parent);
        $node->setTreeNodeName($treeNodeCategoryName);

        return $node;
    }

    public static function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add((string) $sx['name'], $parent);
    }
}
