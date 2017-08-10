<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Support\Manager as CoreManager;

class PagerManager extends CoreManager
{
    protected function createApplicationDriver()
    {
        return new ConcreteBootstrap3PagerView();
    }

    protected function createDashboardDriver()
    {
        return new ConcreteBootstrap3PagerView();
    }
}
