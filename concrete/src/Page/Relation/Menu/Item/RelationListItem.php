<?php

namespace Concrete\Core\Page\Relation\Menu\Item;

use Concrete\Core\Application\UserInterface\Menu\Item\Item;

class RelationListItem extends Item
{

    public function __construct()
    {
        $page = \Page::getCurrentPage();
        $app = \Core::make('app');
        $controller = $app->make('Concrete\Core\Page\Relation\Menu\Item\RelationListController', array($page));
        $this->setController($controller);
        $this->setPosition('right');
    }
}
