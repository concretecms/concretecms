<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Entity\Automation\Process;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

final class Version20201016023919 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/automation', 'Automation');
        $this->createSinglePage('/dashboard/system/automation/tasks', 'Tasks', ['meta_keywords' => 'automated jobs, tasks, commands, console, cli']);
        $this->createSinglePage('/dashboard/system/automation/processes', 'Processes', ['meta_keywords' => 'queues, jobs, running']);
        $this->createSinglePage('/dashboard/system/automation/settings', 'Automation Settings');

        $this->refreshEntities([
            Batch::class,
            Process::class,
            Task::class
       ]);

        $this->output(t('Installing automated tasks upgrade XML...'));
        $importer = new ContentImporter();
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/base/tasks.xml');
    }
}
