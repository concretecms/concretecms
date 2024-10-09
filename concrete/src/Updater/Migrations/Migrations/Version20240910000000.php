<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20240910000000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/files/file_chooser', 'File Chooser Options');

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }

}
