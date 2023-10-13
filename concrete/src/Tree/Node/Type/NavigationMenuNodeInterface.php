<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Navigation\Item\ItemInterface;

interface NavigationMenuNodeInterface
{


    public function canViewNavigationItem(): bool;

    public function getNavigationItem(): ItemInterface;


}
