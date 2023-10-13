<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\NavigationMenu\EditPageItem;
use Concrete\Core\Tree\Menu\Menu;
use Concrete\Core\Tree\Node\Type\Page;

class MenuPageMenu extends Menu
{

    public function __construct(Page $item)
    {
        parent::__construct($item);
        $p = new Checker($item);
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditPageItem($item));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($item));
        }
    }

}