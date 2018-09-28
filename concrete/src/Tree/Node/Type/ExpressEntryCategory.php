<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\Tree\Node\Type\Formatter\ExpressEntryResultsListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\CategoryMenu;
use Concrete\Core\Tree\Node\Type\Menu\ExpressEntryCategoryMenu;
use Concrete\Core\Tree\Node\Type\Menu\ExpressEntryLocationMenu;
use Loader;

class ExpressEntryCategory extends Category
{
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\ExpressTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\ExpressTreeNodeAssignment';
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'express_tree_node';
    }

    public function getTreeNodeMenu()
    {
        return new ExpressEntryCategoryMenu($this);
    }

    public function getTreeNodeTypeName()
    {
        return 'Express Entry Results';
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
            return t('Entries');
        }
    }

    public function getListFormatter()
    {
        return new ExpressEntryResultsListFormatter();
    }


}
