<?php

namespace Concrete\Core\Tree\Menu\Item;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Concrete\Core\Tree\Node\Node;
use HtmlObject\Element;
use HtmlObject\Link;

abstract class AbstractItem implements ItemInterface
{

    abstract public function getItemName();
    abstract public function getAction();
    abstract public function getDialogTitle();
    abstract public function getActionURL();

    public function getItemElement()
    {
        $element = new Element('li');
        $link = new Link('#', $this->getItemName());
        $link->setAttribute('data-tree-action', $this->getAction());
        $link->setAttribute('dialog-title', $this->getDialogTitle());
        $link->setAttribute('data-tree-action-url', $this->getActionURL());
        $element->appendChild($link);
        return $element;
    }

}