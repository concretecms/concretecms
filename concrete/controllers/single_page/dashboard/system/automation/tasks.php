<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Page\Controller\DashboardPageController;
use Punic\Comparer;

class Tasks extends DashboardPageController
{
    public function view()
    {
        $tasks = $this->entityManager->getRepository(Task::class)
            ->findAll();
        $comparer = new Comparer();
        usort($tasks, function($a, $b) use ($comparer) {
            return $comparer->compare($a->getController()->getName(), $b->getController()->getName());
        });
        $this->set('tasks', $tasks);
    }
}
