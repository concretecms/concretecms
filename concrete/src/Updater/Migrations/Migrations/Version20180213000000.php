<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20180213000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Locale::class,
        ]);
    }
}
