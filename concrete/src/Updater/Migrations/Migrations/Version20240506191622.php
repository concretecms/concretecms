<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20240506191622 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // This was an interim step: But now that we're updating to 9.3 and beyond we do _not_ want to
        // prime people for the update. They're getting the update!
        // $service = $this->app->make(AnnouncementService::class);
        // $service->createAnnouncementIfNotExists('concrete_version_929');
        $this->refreshEntities([
            // Update varchar to text
            Client::class,
        ]);
    }
}
