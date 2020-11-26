<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Page\Controller\DashboardPageController;
use Punic\Comparer;

class Tasks extends DashboardPageController
{
    public function view()
    {
        $tasks = $this->app->make(TaskService::class)->getList();
        $this->set('tasks', $tasks);
    }
}
