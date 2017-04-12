<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Users extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/users/search');
    }
}
