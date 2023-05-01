<?php

namespace Concrete\Core\Tree\Menu\Item\NavigationMenu;

use Concrete\Core\Tree\Menu\Item\AbstractNodeItem;

class EditDashboardPageItem extends AbstractNodeItem
{

    public function getDialogTitle()
    {
        return t('Edit Menu Link');
    }

    public function getAction()
    {
        return 'edit-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/edit/dashboard_page?treeNodeID=' . $this->node->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Edit Menu Link');
    }


}