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
        $this->output(t('Refreshing express details block.'));
        $bt = BlockType::getByHandle('express_entry_detail');
        if (is_object($bt)) {
            $bt->refresh();
        }
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
    }

    public function down(Schema $schema)
    {
    }
}
