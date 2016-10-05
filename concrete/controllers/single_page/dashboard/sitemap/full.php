<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Loader;

class Full extends DashboardSitePageController
{
    public function view()
    {
        $this->requireAsset('core/sitemap');
        $dh = Loader::helper('concrete/dashboard/sitemap');
        $this->set('includeRootPages', $dh->includeRootPages());
        $this->set('site', $this->getSite());
    }
}
