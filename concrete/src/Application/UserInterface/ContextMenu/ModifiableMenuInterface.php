<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;

interface ModifiableMenuInterface extends MenuInterface
{

    function addItem(ItemInterface $item);

}