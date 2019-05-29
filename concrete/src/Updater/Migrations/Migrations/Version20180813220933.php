<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180813220933 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_entry_list');
    }


}
