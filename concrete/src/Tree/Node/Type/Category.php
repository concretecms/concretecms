<?php

namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\CategoryMenu;

class Category extends TreeNode
{
    public function getTreeNodeTranslationContext()
    {
        return 'TreeNodeCategoryName';
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\CategoryTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\CategoryTreeNodeAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'category_tree_node';
    }

    public function getTreeNodeMenu()
    {
        return new CategoryMenu($this);
    }

    public function getTreeNodeTypeName()
    {
        return 'Category';
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
            $obj->folder = true;
            $p = new \Permissions($this);
            $data = $this->getTreeObject()->getRequestData();
            if (is_array($data) && !empty($data['allowFolderSelection'])) {
                $obj->hideCheckbox = false;
            } else {
                $obj->hideCheckbox = true;
            }
            $obj->icon = 'fa fa-folder';
            $obj->canAddTopicTreeNode = $p->canAddTopicTreeNode();
            $obj->canAddCategoryTreeNode = $p->canAddCategoryTreeNode();

            return $obj;
        }
    }

    public function getListFormatter()
    {
        return new CategoryListFormatter();
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
