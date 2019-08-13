<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;

/**
 * @since 8.0.0
 */
interface ModifiableMenuInterface extends MenuInterface
{

    function addItem(ItemInterface $item);

}