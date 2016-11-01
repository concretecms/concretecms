<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

interface ManagerInterface
{

    function getMenu($mixed);
    function deliverMenu(ModifiableMenuInterface $menu);

}