<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

use Concrete\Core\Tree\Menu\Item\DeleteItem;

/**
 * @since 8.2.0
 */
class DeleteExpressEntryCategoryItem extends DeleteItem
{

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/category/delete_express?treeNodeID=' . $this->node->getTreeNodeID());
    }


}