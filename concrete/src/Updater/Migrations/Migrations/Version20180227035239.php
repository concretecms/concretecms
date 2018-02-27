<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180227035239 extends AbstractMigration
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
