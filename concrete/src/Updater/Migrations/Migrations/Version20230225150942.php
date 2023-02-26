<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Update\Announcement;
use Concrete\Core\Updater\Announcement\AnnouncementService;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20230225150942 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities(
            [
                Announcement::class
            ]
        );
        $service = $this->app->make(AnnouncementService::class);
        $service->registerCoreVersion('9.2.0');
    }

}
