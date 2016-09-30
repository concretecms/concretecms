<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactoryInterface;

class Users extends DashboardPageController
{
    public function view()
    {
        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->app->make('url/manager')->resolve(['/dashboard/users/search'])
        );
    }
}
