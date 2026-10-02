<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class Version20260930073300 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $task = $entityManager->getRepository(Task::class)->findOneByHandle('delete_orphaned_conversation_attachments');
        if ($task) {
            return;
        }
        $task = new Task();
        $task->setHandle('delete_orphaned_conversation_attachments');
        $entityManager->persist($task);
        $entityManager->flush();
        $taskSetService = $this->app->make(TaskSetService::class);
        $taskSet = $taskSetService->getByHandle('maintenance');
        if ($taskSet && !$taskSetService->taskSetContainsTask($taskSet, $task)) {
            $taskSetService->addTaskToSet($task, $taskSet);
        }
    }
}
