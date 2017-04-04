<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Loader;

class Full extends DashboardPageController
{
    public function view()
    {
        $this->requireAsset('core/sitemap');
        $dh = Loader::helper('concrete/dashboard/sitemap');
        $this->set('includeSystemPages', $dh->includeSystemPages());
        $this->set('flag', $this->app->make(Flag::class));
    }
}
