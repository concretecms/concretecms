<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170118000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->addVersionIdToPageTypeOutputBlocks();
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function addVersionIdToPageTypeOutputBlocks()
    {
        $this->output(t('Adding cvID to PageTypeComposerOutputBlocks...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'PageTypeComposerOutputBlocks',
        ]);

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
}
