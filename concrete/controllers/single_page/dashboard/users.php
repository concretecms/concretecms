<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RedirectResponse;

class Users extends DashboardPageController
{
    public function validateRequest()
    {
        return new RedirectResponse($this->app->make('url/manager')->resolve(['/dashboard/users/search']));
    }
}
