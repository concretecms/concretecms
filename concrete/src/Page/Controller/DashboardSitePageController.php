<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Validation\CSRF\Token;
use Loader;

class DashboardSitePageController extends DashboardPageController
{

    protected function getSite()
    {
        return $this->site;
    }

    public function on_start()
    {
        parent::on_start();
        $this->site = $this->app->make('site')->getActiveSiteForEditing();
    }
}
