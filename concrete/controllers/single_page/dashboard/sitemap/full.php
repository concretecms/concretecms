<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\RedirectResponse;
use Loader;

class Full extends DashboardPageController
{
    public function view()
    {
        $this->requireAsset('core/sitemap');
        $dh = Loader::helper('concrete/dashboard/sitemap');
        $cookie = $this->app->make('cookie');
        $this->set('includeSystemPages', $dh->includeSystemPages());
        $this->set('displayDoubleSitemap', $cookie->get('displayDoubleSitemap'));
        $this->set('flag', $this->app->make(Flag::class));
    }

    public function include_system_pages($include = 0)
    {
        $dh = Loader::helper('concrete/dashboard/sitemap');
        if ($include == 1) {
            $dh->setIncludeSystemPages(true);
        } else {
            $dh->setIncludeSystemPages(false);
        }
        $response = new RedirectResponse(\URL::to('/dashboard/sitemap/full'));
        return $response;
    }

    public function display_double_sitemap($display = 0)
    {
        $cookie = $this->app->make('cookie');
        if ($display == 1) {
            $cookie->set('displayDoubleSitemap', 1);
        } else {
            $cookie->set('displayDoubleSitemap', 0);
        }
        $response = new RedirectResponse(\URL::to('/dashboard/sitemap/full'));
        return $response;
    }

}
