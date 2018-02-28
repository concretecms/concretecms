<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20180227035239 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
        $table = $schema->getTable('CollectionVersions');
        if (!$table->hasColumn('cvPublishEndDate')) {
            $table->addColumn('cvPublishEndDate', 'datetime', ['notnull' => false]);
        }
    }
}
