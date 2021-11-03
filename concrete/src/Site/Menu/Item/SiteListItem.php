<?php

namespace Concrete\Core\Site\Menu\Item;

use Concrete\Core\Application\UserInterface\Menu\Item\Item;

class SiteListItem extends Item
{

    public function __construct()
    {
        parent::__construct('site_list');

        $page = \Page::getCurrentPage();
        $app = \Core::make('app');
        $controller = $app->make(SiteListController::class, array('page' => $page));
        $this->setController($controller);
        $this->setPosition('left');
    }
}
