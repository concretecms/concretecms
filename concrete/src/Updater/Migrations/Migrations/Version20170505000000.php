<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

/**
 * @since 8.2.0
 */
class Version20170505000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     * @since 8.3.2
     */
    public function upgradeSchema(Schema $schema)
    {
        $stacks = $schema->getTable('Stacks');
        if ($stacks->hasColumn('siteTreeID')) {
            $stacks->dropColumn('siteTreeID');
        }
    }
}
