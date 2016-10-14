<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Modifier;

use Concrete\Core\Application\UserInterface\ContextMenu\ModifiableMenuInterface;

interface ModifierInterface
{
    function modifyMenu(ModifiableMenuInterface $menu);
}