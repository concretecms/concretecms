<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;

class Version20181112211702 extends AbstractMigration
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\Express\Control\AssociationControl',
        ]);
    }
}