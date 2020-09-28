<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20200928000001 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {

        $c = $this->createSinglePage('/dashboard/system/mail/logging', 'Email Logging');

        if (is_object($c) && !$c->isError()) {
            $c->update([
                'cDescription' => t('Control how emails get logged.')
            ]);
        }

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();

    }
}