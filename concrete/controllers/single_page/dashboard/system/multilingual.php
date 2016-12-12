<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Multilingual extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/system/multilingual/setup');
    }
}
