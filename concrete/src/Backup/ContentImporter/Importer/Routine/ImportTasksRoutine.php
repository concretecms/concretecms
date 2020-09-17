<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Automation\Task;
use Doctrine\ORM\EntityManager;

class ImportTasksRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'tasks';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->tasks)) {
            $entityManager = app(EntityManager::class);
            foreach ($sx->tasks->task as $xml) {
                $pkg = static::getPackageObject($xml['package']);
                $task = $entityManager->getRepository(Task::class)
                    ->findOneByHandle((string) $xml['handle']);
                if (!$task) {
                    $task = new Task();
                    $task->setHandle((string) $xml['handle']);
                    if ($pkg) {
                        $task->setPackage($pkg);
                    }
                    $entityManager->persist($task);
                }
            }
            $entityManager->flush();
        }

    }

}
