<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Scheduler\Scheduler;
use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Page\Controller\DashboardPageController;

class Tasks extends DashboardPageController
{
    public function view()
    {
        $taskSets = $this->app->make(TaskSetService::class)->getGroupedTasks();
        $this->set('taskSets', $taskSets);
        $this->set('schedulingEnabled', $this->app->make(Scheduler::class)->isEnabled());
    }
}
