<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210813173441 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Nothing here. Had to move this back to Version20210216184000
    }

}
