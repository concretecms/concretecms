<?php

namespace Concrete\Core\Command\Process\Menu\Item;

use Concrete\Core\Application\UserInterface\Menu\Item\Item;

class RunningProcessesItem extends Item
{

    public function __construct()
    {
        parent::__construct('running_processes');

        $page = \Page::getCurrentPage();
        $app = \Core::make('app');
        $controller = $app->make(RunningProcessesController::class);
        $this->setController($controller);
        $this->setPosition('right');
    }
}
