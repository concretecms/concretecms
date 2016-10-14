<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ConditionalItemInterface;

abstract class AbstractManager implements ManagerInterface
{

    protected $runtimeItems = array();

    public function addMenuItem(ConditionalItemInterface $item)
    {
        $this->runtimeItems[] = $item;
    }

}