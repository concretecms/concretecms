<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210622145600 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {

        $this->createSinglePage('/dashboard/system/registration/session_options', 'Session Options');

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}