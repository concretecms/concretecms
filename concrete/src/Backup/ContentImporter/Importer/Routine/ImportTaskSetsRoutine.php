<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Automation\TaskSet;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;
use SimpleXMLElement;

class ImportTaskSetsRoutine extends AbstractRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'task_sets';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::import()
     */
    public function import(SimpleXMLElement $sx)
    {
        if (isset($sx->tasksets)) {
            $app = Application::getFacadeApplication();
            $taskRepository = $app->make(EntityManager::class)->getRepository(Task::class);
            $service = $app->make(TaskSetService::class);
            foreach ($sx->tasksets->taskset as $setNode) {
                $set = $service->getByHandle((string) $setNode['handle']);
                if (!$set) {
                    $pkg = static::getPackageObject($setNode['package']);
                    $set = $service->add((string) $setNode['handle'], (string) $setNode['name'], $pkg);
                }
                foreach ($setNode->children() as $setTaskNode) {
                    $task = $taskRepository->findOneByHandle((string) $setTaskNode['handle']);
                    if ($task) {
                        if (!$service->taskSetContainsTask($set, $task)) {
                            $service->addTaskToSet($task, $set);
                        }
                    }
                }
            }
        }
    }
}
