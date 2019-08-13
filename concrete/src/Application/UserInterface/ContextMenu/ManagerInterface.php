<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

/**
 * @since 8.0.0
 */
interface ManagerInterface
{

    function getMenu($mixed);
    function deliverMenu(ModifiableMenuInterface $menu);

}