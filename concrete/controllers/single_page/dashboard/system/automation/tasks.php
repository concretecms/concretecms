<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Page\Controller\DashboardPageController;

class Tasks extends DashboardPageController
{
    public function view()
    {
        $tasks = $this->entityManager->getRepository(Task::class)
            ->findAll();
        $this->set('tasks', $tasks);
    }
}
