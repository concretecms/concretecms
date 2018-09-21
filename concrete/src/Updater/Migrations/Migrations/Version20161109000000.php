<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20161109000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.0.0';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $this->version->getConfiguration()->getOutputWriter()->write(t('Adding fields to workflow and select tables...'));
        if (!$schema->getTable('Workflows')->hasColumn('pkgID')) {
            $schema->getTable('Workflows')->addColumn('pkgID', 'integer', [
                'unsigned' => true, 'notnull' => true, 'default' => 0,
            ]);
        }
        if (!$schema->getTable('atSelectOptions')->hasColumn('isDeleted')) {
            $schema->getTable('atSelectOptions')->addColumn('isDeleted', 'boolean', [
                'default' => 0,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->version->getConfiguration()->getOutputWriter()->write(t('Updating tables found in doctrine xml...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'FileImageThumbnailPaths',
        ]);
    }
}
