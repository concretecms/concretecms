<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20210622145600 extends AbstractMigration
{

    public function upgradeDatabase()
    {

        $this->createSinglePage('/dashboard/system/registration/session_options', 'Session Options');

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}