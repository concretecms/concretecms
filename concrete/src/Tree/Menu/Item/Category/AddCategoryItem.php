<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

/**
 * @since 8.0.0
 */
class AddCategoryItem extends CategoryItem
{

    public function getDialogTitle()
    {
        return t('Add Category');
    }

    public function getAction()
    {
        return 'add-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/add/category?treeNodeID=' . $this->category->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Add Category');
    }


}