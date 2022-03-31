<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20220331222557 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_form');
        // Default the boolean to true. This was set in a previous migration but we can't reference it until a later migration.
        $this->connection->executeStatement('update ExpressEntities set is_published = 1');
    }

}
