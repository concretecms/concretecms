<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

/**
 * @since 8.0.0
 */
class Menu extends AbstractMenu
{

    public function jsonSerialize()
    {
        $html = (string) $this->getMenuElement();
        return $html;
    }


}