<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Users extends DashboardPageController
{
    public function validateRequest()
    {
        $this->redirect('/dashboard/users/search');
    }
}
