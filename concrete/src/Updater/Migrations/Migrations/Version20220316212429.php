<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20220316212429 extends AbstractMigration
{

    public function upgradeDatabase()
    {
        $this->refreshEntities(
            [
                Entity::class,
            ]
        );

        // Default the boolean to true
        $this->connection->executeStatement('update ExpressEntities set is_published = 1');
    }

}
