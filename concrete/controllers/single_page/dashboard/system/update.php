<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use \Concrete\Core\Page\Controller\DashboardPageController;

/**
 * @since 8.2.0
 */
class Update extends DashboardPageController
{
    /**
     * Dashboard view - automatically redirects to a default
     * page in the category.
     */
    public function view()
    {
        $this->redirect('/dashboard/system/update/update');
    }
}
