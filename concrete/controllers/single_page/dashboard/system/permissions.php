<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Permissions extends DashboardPageController
{
    /**
     * Dashboard view - automatically redirects to a default
     * page in the category.
     */
    public function view()
    {
        return $this->buildRedirect('/dashboard/system/permissions/site');
    }
}
