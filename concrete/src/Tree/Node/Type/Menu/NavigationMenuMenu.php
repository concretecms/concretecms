<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Tree\Menu\Item\NavigationMenu\AddPageItem;
use Concrete\Core\Tree\Menu\Menu;
use Concrete\Core\Tree\Node\Type\Category;

class NavigationMenuMenu extends Menu
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
        $p = new \Permissions($category);
        if ($p->canAddTopicTreeNode()) {
            $this->addItem(new AddPageItem($category));
        }
    }

}