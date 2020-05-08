<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

class PopoverMenu extends DropdownMenu
{

    public function getMenuElement()
    {
        $dropdown = parent::getMenuElement();
        $menu = new Element('div', null, $this->menuAttributes);
        $menu->addClass('popover')
            ->addClass('fade');
        $menu->appendChild(
            (new Element('div'))->addClass('arrow')
        );

        $inner = (new Element('div'))->addClass('popover-inner');
        $inner->appendChild($dropdown);
        $menu->appendChild($inner);
        return $menu;
    }


}
