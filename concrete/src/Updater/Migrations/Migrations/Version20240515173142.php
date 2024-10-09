<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20240515173142 extends AbstractMigration
{
    public function upgradeDatabase()
    {
        $service = $this->app->make(AnnouncementService::class);
        $service->createAnnouncementIfNotExists('concrete_version_930');
        try {
            $repository = $this->app->make(PackageRepositoryInterface::class);
            $repository->connect();
        } catch (\Exception $e) {
            // Fail silently, don't halt upgrading if we can't connect
        }
    }
}
