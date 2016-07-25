<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

class EditCategoryItem extends CategoryItem
{



    public function getDialogTitle()
    {
        return t('Edit Category');
    }

    public function getAction()
    {
        return 'edit-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/edit/category?treeNodeID=' . $this->category->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Edit Category');
    }


}