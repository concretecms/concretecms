<?php

namespace Concrete\Core\Tree\Menu\Item;

class DeleteItem extends AbstractNodeItem
{

    public function getDialogTitle()
    {
        return t('Delete %s', $this->node->getTreeNodeTypeName());
    }

    public function getAction()
    {
        return 'delete-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/delete?treeNodeID=' . $this->node->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Delete', $this->node->getTreeNodeTypeName());
    }

}