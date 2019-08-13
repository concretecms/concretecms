<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

/**
 * @since 8.2.0
 */
class AddExpressEntryCategoryItem extends AddCategoryItem
{

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/add/category?treeNodeID=' . $this->category->getTreeNodeID() . '&treeNodeTypeHandle=express_entry_category');
    }

}