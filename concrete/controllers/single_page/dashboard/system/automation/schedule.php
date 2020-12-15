<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Scheduler\Scheduler;
use Concrete\Core\Page\Controller\DashboardPageController;

class Schedule extends DashboardPageController
{
    public function view()
    {
        $this->set('enabled', $this->app->make(Scheduler::class)->isEnabled());
    }
}
