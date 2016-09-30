<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactoryInterface;

class Reports extends DashboardPageController
{
    public function validateRequest()
    {
        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->app->make('url/manager')->resolve(['/dashboard/reports/forms'])
        );
    }
}
