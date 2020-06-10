<?php

namespace Concrete\Core\Tree\Menu\Item;

use HtmlObject\Element;
use HtmlObject\Link;

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

    public function getItemElement()
    {
        $link = new Link('#', $this->getItemName(), ['class' => 'dropdown-item']);
        $link->setAttribute('data-tree-action', $this->getAction());
        $link->setAttribute('dialog-title', $this->getDialogTitle());
        $link->setAttribute('data-tree-action-url', $this->getActionURL());
        $link->setAttribute('dialog-width', '520');
        $link->setAttribute('dialog-height', '450');
        return $link;
    }
}
