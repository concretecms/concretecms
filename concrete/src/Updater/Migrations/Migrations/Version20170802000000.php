<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170802000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.2.1';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // First, we prune out all duplicates. We get the
        // Listing from the table as it comes out of the database
        // and we remove any rows that are after the first if they match.

        $r = $this->connection->executeQuery('select * from btExpressEntryDetail');
        $processed = [];
        $delete = [];
        while ($row = $r->fetch()) {
            if (!in_array($row['bID'], $processed)) {
                $processed[] = $row['bID'];
            } else {
                $delete[] = [
                    $row['bID'],
                    $row['exEntityID'],
                    $row['exSpecificEntryID'],
                    $row['exFormID'],
                ];
            }
        }

        foreach ($delete as $deleteRow) {
            $this->connection->executeQuery('delete from btExpressEntryDetail where bID = ? and exEntityID = ? and exSpecificEntryID = ? and exFormID = ?', $deleteRow);
        }

        // Now that we have removed problematic duplicate rows, rescan the table and remove the primary keys.

        $this->refreshBlockType('express_entry_detail');
    }
}
