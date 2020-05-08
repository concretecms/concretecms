<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

class DropdownMenu extends AbstractMenu
{
    public function getMenuElement()
    {
        $menu = new Element('div', null, $this->menuAttributes);
        $menu->addClass('dropdown-menu');
        foreach($this->items as $item) {
            $menu->appendChild($item->getItemElement());
        }
        return $menu;
    }



}
