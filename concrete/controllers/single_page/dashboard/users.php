<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Users extends DashboardPageController
{
    /**
     * @since 8.1.0
     */
    public function view()
    {
        $this->redirect('/dashboard/users/search');
    }
}
