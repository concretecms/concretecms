<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Loader;

class Full extends DashboardSitePageController
{
    public function view()
    {
        $this->requireAsset('core/sitemap');
        $dh = Loader::helper('concrete/dashboard/sitemap');
        $this->set('includeSystemPages', $dh->includeSystemPages());
        $this->set('site', $this->getSite());
        $this->set('locales', $this->getSite()->getLocales());
        $this->set('flag', $this->app->make(Flag::class));
    }
}
