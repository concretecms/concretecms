<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161208000000 extends AbstractMigration
{

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
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'TreeSearchQueryNodes',
        ));
    }

    protected function updateEmptyFileAttributes()
    {
        $this->output(t('Updating old empty file attributes.'));
        $this->connection->executeQuery('update atFile set fID = null where fID = 0');
    }

    public function up(Schema $schema)
    {
        $this->updateBlocks();
        $this->updateEmptyFileAttributes();
        $this->updateDoctrineXmlTables();
    }

    public function down(Schema $schema)
    {
    }
}
