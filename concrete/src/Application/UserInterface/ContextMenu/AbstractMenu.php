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

    public function getMenuElement()
    {
        if ($this->items->count() > $this->minItemThreshold) {
            $menu = new Element('div', null, $this->menuAttributes);
            $menu->addClass('popover')
                ->addClass('fade');
            $menu->appendChild(
                (new Element('div'))->addClass('arrow')
            );

            $inner = (new Element('div'))->addClass('popover-inner');
            $list = (new Element('ul'))->addClass('dropdown-menu');

            /**
             * @var $item ItemInterface
             */
            foreach($this->items as $item) {
                $list->appendChild($item->getItemElement());
            }

            $inner->appendChild($list);
            $menu->appendChild($inner);
            return $menu;
        }
    }

}