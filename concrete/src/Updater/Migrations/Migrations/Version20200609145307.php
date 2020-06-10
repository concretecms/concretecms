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
        // Note, this is commented out in the version 9 branch, because in version 9 we don't disable multisite.
        // I didn't want to include this migration at all in version 9 but due to the way we merged it had to
        // happen, and I'd rather not remove it in case that causes issues with sites that have already applied
        // the migration. So I think it's best to just leave it but comment it out.
        
        /*
        $config = $this->app->make('config');
        $enabled = $config->get('concrete.multisite.enabled');
        if ($enabled) {
            $config->save('concrete.multisite.enabled', false);
        }
        */

    }
}
