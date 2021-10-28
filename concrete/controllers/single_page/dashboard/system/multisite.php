<?php

namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Site\InstallationService;

class Multisite extends DashboardPageController
{
    public function view()
    {
        $service = $this->app->make(InstallationService::class);
        if ($service->isMultisiteEnabled()) {
            return $this->buildRedirectToFirstAccessibleChildPage();
        }

        return $this->buildRedirect($this->action('settings'));
    }
}
