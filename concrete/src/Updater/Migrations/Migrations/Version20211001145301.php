<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Automation\TaskSet;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;

final class Version20211001145301 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var TaskSetService $taskSetService */
        $taskSetService = $this->app->make(TaskSetService::class);

        $task = $entityManager->getRepository(Task::class)->findOneBy(["handle" => "generate_thumbnails"]);

        if (!$task instanceof Task) {
            $task = new Task();
            $task->setHandle( "generate_thumbnails");
            $entityManager->persist($task);
            $entityManager->flush();

            $taskSet = $taskSetService->getByHandle("maintenance");

            if ($taskSet instanceof TaskSet) {
                if (!$taskSetService->taskSetContainsTask($taskSet, $task)) {
                    $taskSetService->addTaskToSet($task, $taskSet);
                }
            }
        }
    }
}
