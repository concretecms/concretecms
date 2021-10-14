<?php
declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Entity\Automation\TaskSetTask;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Automation\TaskSet;
use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Command\TaskProcess;
use Concrete\Core\Job\Job;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

final class Version20210725000000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/automation', 'Automation');
        $this->createSinglePage('/dashboard/system/notification', 'Notification');
        $this->createSinglePage(
            '/dashboard/system/automation/tasks',
            'Tasks',
            ['meta_keywords' => 'automated jobs, tasks, commands, console, cli']
        );
        $this->createSinglePage(
            '/dashboard/system/automation/activity',
            'Activity',
            ['meta_keywords' => 'processes, queues, jobs, running']
        );
        $this->createSinglePage(
            '/dashboard/system/automation/schedule',
            'Schedule',
            ['meta_keywords' => 'cron, scheduling']
        );
        $this->createSinglePage('/dashboard/system/automation/settings', 'Automation Settings');
        $this->createSinglePage(
            '/dashboard/system/notification/events',
            'Server-Sent Events',
            ['meta_keywords' => 'websocket, socket, socket.io, push, push notifications, mercure']
        );
        $this->createSinglePage(
            '/dashboard/system/notification/alerts',
            'Waiting for Me',
            ['meta_keywords' => 'waiting for me, inbox, notifications']
        );

        $page = Page::getByPath('/dashboard/system/registration/notification');
        if ($page && !$page->isError()) {
            $page->delete();
        }
        
        $this->refreshEntities(
            [
                Task::class,
                TaskSet::class,
                TaskSetTask::class,
                Batch::class,
                Process::class,
                TaskProcess::class
            ]
        );

        $this->output(t('Installing automated tasks upgrade XML...'));
        $importer = new ContentImporter();
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/base/tasks.xml');

        $jobsToUninstall = [
            'fill_thumbnails_table',
            'update_gatherings',
        ];
        foreach($jobsToUninstall as $jHandle) {
            $job = Job::getByHandle($jHandle);
            if ($job) {
                $job->uninstall();
            }
        }

        // add breadcrumbs block type
        $bt = BlockType::getByHandle('breadcrumbs');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('breadcrumbs');

            // add breadcrumbs block to navigation set
            $set = BlockTypeSet::getByHandle('navigation');
            if (is_object($set)) {
                $set->addBlockType($bt);
            }
        }
    }
}
