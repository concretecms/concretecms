<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161109000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->version->getConfiguration()->getOutputWriter()->write(t('Updating tables found in doctrine xml...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'FileImageThumbnailPaths'
        ));

        $this->version->getConfiguration()->getOutputWriter()->write(t('Adding fields to workflow and select tables...'));
        if (!$schema->getTable('Workflows')->hasColumn('pkgID')) {
            $schema->getTable('Workflows')->addColumn('pkgID', 'integer', array(
                'unsigned' => true, 'notnull' => true, 'default' => 0
            ));
        }
        if (!$schema->getTable('atSelectOptions')->hasColumn('isDeleted')) {
            $schema->getTable('atSelectOptions')->addColumn('isDeleted', 'boolean', array(
                'default' => 0
            ));
        }
    }

    public function down(Schema $schema)
    {
    }
}
