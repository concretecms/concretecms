<?php
namespace Concrete\Core\Board\Instance\Slot\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\AbstractManager;
use Concrete\Core\Entity\Board\InstanceSlot;

class Manager extends AbstractManager
{

    /**
     * @param InstanceSlot $mixed
     * @return Menu
     */
    public function getMenu($mixed)
    {
        return new Menu($mixed);
    }
}
