<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20200818000002 extends AbstractMigration
{
    public function upgradeDatabase()
    {

        $this->output(t('Updating tables found in doctrine xml...'));

        // Update tables that still exist in db.xml
        Schema::refreshCoreXMLSchema([
            'UsedAreas',
        ]);
    }
}