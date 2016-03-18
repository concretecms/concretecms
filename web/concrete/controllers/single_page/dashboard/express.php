<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Express extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/express/entries');
    }
}
