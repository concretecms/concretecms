<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

class Menu extends AbstractMenu
{

    public function jsonSerialize()
    {
        $html = (string) $this->getMenuElement();
        return $html;
    }


}