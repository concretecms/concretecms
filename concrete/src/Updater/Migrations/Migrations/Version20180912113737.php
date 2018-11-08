<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;

class Version20180912113737 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Entry::class,
            ExpressValue::class
        ]);
    }
}
