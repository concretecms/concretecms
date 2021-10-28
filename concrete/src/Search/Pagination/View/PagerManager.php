<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Support\Manager as CoreManager;

class PagerManager extends CoreManager
{
    protected function createApplicationDriver()
    {
        return new ConcreteCMSPagerView();
    }

    protected function createDashboardDriver()
    {
        return new ConcreteCMSPagerView();
    }

    protected function createCmsDriver()
    {
        return new ConcreteCMSPagerView();
    }
}
