<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20240506191622 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $service = $this->app->make(AnnouncementService::class);
        $service->createAnnouncementIfNotExists('concrete_version_929');
        $this->refreshEntities([
            // Update varchar to text
            Client::class,
        ]);
    }
}
