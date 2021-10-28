<?php

namespace Concrete\Core\Tree\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\PopoverMenu;
use Concrete\Core\Tree\Node\Node;

abstract class Menu extends PopoverMenu
{

    public function __construct(Node $node)
    {
        parent::__construct();
        $this->setAttribute('data-tree-menu', $node->getTreeID());
    }


}
