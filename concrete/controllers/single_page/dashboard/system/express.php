<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

/**
 * @since 8.0.0
 */
class Express extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/system/express/entities');
    }
}
