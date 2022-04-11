<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20201130130221 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Nothing here. I'm keeping the migration because it's just easier that way but I had to move it
        // into Version20200523044444 instead because it has to happen BEFORE the first call to an attribute
        // in the upgrade from 8.5.x -> 9.
    }
}
