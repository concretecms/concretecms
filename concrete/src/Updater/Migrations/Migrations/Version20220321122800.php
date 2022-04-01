<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20220321122800 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('hero_image');
        $this->refreshEntities(
            [
                ExpressKey::class,
                Entity::class,
            ]
        );
        // Default the boolean to true
        $this->connection->executeStatement('update ExpressEntities set is_published = 1');
    }

}
