<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20200609145307 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Disable multisite if it was turned on. It is not ready < 9.0.
        $config = $this->app->make('config');
        $enabled = $config->get('concrete.multisite.enabled');
        if ($enabled) {
            $config->save('concrete.multisite.enabled', false);
        }
    }
}
