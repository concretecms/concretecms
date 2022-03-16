<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Automation\TaskSet;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;

final class Version20211028000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeDatabase()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $task = $entityManager->getRepository(Task::class)->findOneByHandle('remove_unvalidated_users');
        if (!$task) {
            $task = new Task();
            $task->setHandle('remove_unvalidated_users');
            $entityManager->persist($task);
            $entityManager->flush();
            /** @var TaskSetService $taskSetService */
            $taskSetService = $this->app->make(TaskSetService::class);
            /** @var TaskSet $taskSet */
            $taskSet = $taskSetService->getByHandle('user_groups');
            if ($taskSet && !$taskSetService->taskSetContainsTask($taskSet, $task)) {
                $taskSetService->addTaskToSet($task, $taskSet);
            }
        }
    }
}
