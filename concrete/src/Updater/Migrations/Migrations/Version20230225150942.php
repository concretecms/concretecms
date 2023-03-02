<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Entity\Announcement\Announcement;
use Concrete\Core\Entity\Announcement\AnnouncementUserView;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20230225150942 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities(
            [
                Announcement::class,
                AnnouncementUserView::class,
            ]
        );
        $service = $this->app->make(AnnouncementService::class);
        $service->createAnnouncementIfNotExists('concrete_version_920');
    }

}
