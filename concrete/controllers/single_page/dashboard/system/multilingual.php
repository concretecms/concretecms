<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

/**
 * @since 5.7.3
 */
class Multilingual extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/system/multilingual/setup');
    }
}
