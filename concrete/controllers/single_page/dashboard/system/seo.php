<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Seo extends DashboardPageController
{
    /**
     * Dashboard view - automatically redirects to a default
     * page in the category.
     */
    public function view()
    {
        $this->redirect('/dashboard/system/seo/urls');
    }
}
