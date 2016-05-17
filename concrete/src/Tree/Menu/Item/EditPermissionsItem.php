<?php

namespace Concrete\Core\Tree\Menu\Item;

class EditPermissionsItem extends AbstractNodeItem
{

    public function getDialogTitle()
    {
        return t('Edit Permissions');
    }

    public function getAction()
    {
        return 'edit-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/permissions?treeNodeID=' . $this->node->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Edit Permissions');
    }
}
