<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Pages extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/pages/themes');
    }
}
