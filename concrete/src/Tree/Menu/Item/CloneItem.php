<?php

namespace Concrete\Core\Tree\Menu\Item;

class CloneItem extends AbstractNodeItem
{

    public function getDialogTitle()
    {
        return null;
    }

    public function getAction()
    {
        return 'clone-node';
    }

    public function getActionURL()
    {
        return null;
    }

    public function getItemName()
    {
        return t('Clone %s', $this->node->getTreeNodeTypeName());
    }

    public function getItemElement()
    {
        $element = parent::getItemElement();
        $element->getChildren()[0]->setAttribute('data-tree-node-id', $this->node->getTreeNodeID());
        return $element;
    }


}