<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20161208000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.0.2';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->updateBlocks();
        $this->updateEmptyFileAttributes();
        $this->updateDoctrineXmlTables();
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function updateBlocks()
    {
        $this->refreshBlockType('express_entry_detail');
        $this->refreshBlockType('page_attribute_display');
    }

    protected function updateDoctrineXmlTables()
    {
        $this->output(t('Updating tables found in doctrine xml...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'TreeSearchQueryNodes',
        ]);
    }

    protected function updateEmptyFileAttributes()
    {
        $this->output(t('Updating old empty file attributes.'));
        $this->connection->executeQuery('update atFile set fID = null where fID = 0');
    }
}
