<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use HtmlObject\Element;

abstract class AbstractMenu implements ModifiableMenuInterface
{

    protected $items;
    protected $menuAttributes = array();
    protected $minItemThreshold = 0;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function setAttribute($key, $value)
    {
        $this->menuAttributes[$key] = $value;
    }

    public function addItem(ItemInterface $item)
    {
        $this->items->add($item);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $html = (string) $this->getMenuElement();
        return $html;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }


}
