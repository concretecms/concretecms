<?php
namespace Concrete\Core\Board\Instance\Slot\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\AbstractManager;
use Concrete\Core\Board\Instance\Slot\RenderedSlot;

class Manager extends AbstractManager
{

    /**
     * @param RenderedSlot $mixed
     * @return Menu
     */
    public function getMenu($mixed)
    {
        return new Menu($mixed);
    }
}
