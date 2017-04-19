<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170118000000 extends AbstractMigration
{

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function addVersionIdToPageTypeOutputBlocks()
    {
        $this->output(t('Adding cvID to PageTypeComposerOutputBlocks...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'PageTypeComposerOutputBlocks',
        ));

        $db = $this->connection;
        $r = $db->executeQuery('select cID, bID, arHandle from PageTypeComposerOutputBlocks');
        while ($row = $r->fetch()) {
            $cvID = $db->fetchColumn('select cvID from CollectionVersionBlocks where cID = ? and bID = ? and arHandle = ?',
                [$row['cID'], $row['bID'], $row['arHandle']]
            );
            if (!$cvID) {
                $cvID = 0;
            }
            $db->executeQuery('update PageTypeComposerOutputBlocks set cvID = ? where cID = ? and bID = ? and arHandle = ?',
                [$cvID, $row['cID'], $row['bID'], $row['arHandle']]
            );
        }
    }

    public function up(Schema $schema)
    {
        $this->addVersionIdToPageTypeOutputBlocks();
    }

    public function down(Schema $schema)
    {
    }
}
