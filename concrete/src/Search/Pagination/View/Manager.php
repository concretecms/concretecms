<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;
use Illuminate\Contracts\Container\Container;

class Manager extends CoreManager
{

    public function __construct(Application $container)
    {
        parent::__construct($container);
    }

    protected function createApplicationDriver()
    {
        return new ConcreteBootstrap5View();
    }

    protected function createDashboardDriver()
    {
        return new ConcreteCMSView();
    }

    protected function createCmsDriver()
    {
        return new ConcreteCMSView();
    }

}
