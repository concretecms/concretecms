<?php
namespace Concrete\Core\Block\Menu;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\ContextMenu\AbstractManager;
use Concrete\Core\Application\UserInterface\ContextMenu\ManagerInterface;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;

class Manager extends AbstractManager
{

    protected $app;
    protected $config;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $app->make('config');
    }

    public function getMenu($mixed)
    {
        $b = $mixed[0];
        $c = $mixed[1];
        $a = $mixed[2];
        return new Menu($this->app, $this->config, $b, $c, $a);
    }
}