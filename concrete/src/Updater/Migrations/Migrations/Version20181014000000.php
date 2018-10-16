<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20181014000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
        $table = $schema->getTable('TreeNodes');
        if (is_object($table) && !$table->hasColumn('fslID')) {
            $table->addColumn('fslID', 'integer', [
                'unsigned' => true, 'notnull' => false,
            ]);
        } elseif (is_object($table) && $table->hasColumn('fslID')) {
            $table->changeColumn('fslID',['unsigned' => true, 'notnull' => false,'default'=>null]);

        }
    }
}
