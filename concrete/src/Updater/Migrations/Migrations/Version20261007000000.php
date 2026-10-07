<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Make the container of the page container instances not nullable.
 *
 * Version20260914000000 did the same, but its schema refresh fails on the MySQL servers with a non-strict sql_mode:
 * this migration replaces it (the old one extends this one, so that the installations stuck on it get the fix too).
 */
class Version20261007000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Container instances are only created for existing containers, and they are deleted together with their container:
        // remove any instance without a container before making the column not nullable
        // (the areas of the removed instances are deleted by the foreign key cascade).
        $this->connection->executeStatement('DELETE FROM PageContainerInstances WHERE containerID IS NULL');
        $schemaManager = $this->connection->getSchemaManager();
        if ($schemaManager->listTableDetails('PageContainerInstances')->getColumn('containerID')->getNotnull()) {
            return;
        }
        // When the sql_mode is not strict, MySQL refuses to change a foreign key column from NULL to NOT NULL
        // (error 1832, see https://bugs.mysql.com/bug.php?id=93838): drop the constraint, the schema refresh creates it again
        // (only if the referenced entity is refreshed too).
        foreach ($schemaManager->listTableForeignKeys('PageContainerInstances') as $foreignKey) {
            if (array_map('strtolower', $foreignKey->getUnquotedLocalColumns()) === ['containerid']) {
                $schemaManager->dropForeignKey($foreignKey, 'PageContainerInstances');
            }
        }
        $this->refreshEntities([Container::class, Instance::class]);
    }
}
