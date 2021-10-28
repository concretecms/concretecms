<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

class PopoverMenu extends AbstractMenu
{

    public function getMenuElement()
    {
        $dropdown = new Element('div');
        $dropdown->addClass('dropdown-menu');
        foreach($this->items as $item) {
            $dropdown->appendChild($item->getItemElement());
        }

        $menu = new Element('div', null, $this->menuAttributes);
        $menu->addClass('popover')
            ->addClass('fade');
        $menu->appendChild(
            (new Element('div'))->addClass('popover-arrow')
        );

        $inner = (new Element('div'))->addClass('popover-inner');
        $inner->appendChild($dropdown);
        $menu->appendChild($inner);
        return $menu;
    }


}
