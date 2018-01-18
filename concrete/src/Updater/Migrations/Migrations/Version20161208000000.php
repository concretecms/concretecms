<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20161208000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
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
        $this->output(t('Refreshing blocks.'));
        $bt = BlockType::getByHandle('express_entry_detail');
        if (is_object($bt)) {
            $bt->refresh();
        }
        $bt = BlockType::getByHandle('page_attribute_display');
        if (is_object($bt)) {
            $bt->refresh();
        }
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
