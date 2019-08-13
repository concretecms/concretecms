<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Modifier;

use Concrete\Core\Application\UserInterface\ContextMenu\ModifiableMenuInterface;

/**
 * @since 8.0.0
 */
interface ModifierInterface
{
    function modifyMenu(ModifiableMenuInterface $menu);
}