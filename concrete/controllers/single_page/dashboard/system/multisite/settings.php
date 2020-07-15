<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Site\InstallationService;

class Settings extends DashboardPageController
{

    public function view()
    {
        $this->set('service', $this->app->make(InstallationService::class));
    }

    public function multisite_required()
    {
        $this->view();
    }


}