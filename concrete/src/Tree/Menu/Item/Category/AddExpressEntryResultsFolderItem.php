<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

/**
 * @since 8.2.0
 */
class AddExpressEntryResultsFolderItem extends AddCategoryItem
{

    public function getDialogTitle()
    {
        return t('Add Results Folder');
    }

    public function getItemName()
    {
        return t('Add Results Folder');
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/add/category?treeNodeID=' . $this->category->getTreeNodeID() . '&treeNodeTypeHandle=express_entry_results');
    }


}