<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class Version20260914000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Container instances are only created for existing containers, and they are deleted together with their container:
        // remove any instance without a container before making the column not nullable
        // (the areas of the removed instances are deleted by the foreign key cascade).
        $this->connection->executeStatement('DELETE FROM PageContainerInstances WHERE containerID IS NULL');
        $this->refreshEntities([Instance::class]);
    }
}
