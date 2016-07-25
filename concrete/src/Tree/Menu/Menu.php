<?php

namespace Concrete\Core\Tree\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Menu as ContextMenu;
use Concrete\Core\Tree\Node\Node;

abstract class Menu extends ContextMenu
{

    public function __construct(Node $node)
    {
        parent::__construct();
        $this->setAttribute('data-tree-menu', $node->getTreeID());
    }


}