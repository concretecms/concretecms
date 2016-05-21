<?php

namespace Concrete\Core\Tree\Menu\Item\Topic;

use Concrete\Core\Tree\Menu\Item\Category\CategoryItem;

class AddTopicItem extends CategoryItem
{

    public function getDialogTitle()
    {
        return t('Add Topic');
    }

    public function getAction()
    {
        return 'add-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/add/topic?treeNodeID=' . $this->category->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Add Topic');
    }


}