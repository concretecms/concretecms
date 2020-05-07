<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

class DropdownMenu extends Menu
{
    public function getMenuElement()
    {
        $menu = (new Element('div'))->addClass("dropdown-menu");
        foreach($this->items as $item) {
            $menu->appendChild($item->getItemElement());
        }
        return $menu;
    }



}
