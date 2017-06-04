<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Support\Manager as CoreManager;

class NextPreviousManager extends CoreManager
{
    protected function createApplicationDriver()
    {
        return new ConcreteBootstrap3NextPreviousView();
    }

    protected function createDashboardDriver()
    {
        return new ConcreteBootstrap3NextPreviousView();
    }
}
