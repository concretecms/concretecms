<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20180627000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
        $table = $schema->getTable('CollectionVersions');
        if (!$table->hasColumn('cvDateApproved')) {
            $table->addColumn('cvDateApproved', 'datetime', ['notnull' => false]);
        }
    }
}
