<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Navigation\Item\DashboardPageItem;
use Concrete\Core\Navigation\Item\ItemInterface;

class DashboardPage extends Page
{

    public function getNavigationItem(): ItemInterface
    {
        return new DashboardPageItem($this->getTreeNodePageObject());
    }

}
