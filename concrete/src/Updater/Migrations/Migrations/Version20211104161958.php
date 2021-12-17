<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Command\ScheduledTask;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20211104161958 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Fix https://github.com/concrete5/concrete5/issues/10040 - we missed it in the 9.0 migration
        $this->refreshEntities([ScheduledTask::class]);
    }
}
