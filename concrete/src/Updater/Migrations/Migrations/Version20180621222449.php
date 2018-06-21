<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;


class Version20180621222449 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $db = $this->connection;
        if ($db->tableExists('atPageSelector')) {
            // This is the name of the page selector attribute table in some implementations of the page selector attribute
            // We need to take this data and place it into atNumber.
            $r = $db->executeQuery('select * from atPageSelector');
            while ($row = $r->fetch()) {
                $db->transactional(function($db) use ($row) {
                    /** @var $db Connection */
                    $avID = $db->fetchColumn('select avID from atNumber where avID = ?', [$row['avID']]);
                    if (!$avID) {
                        $db->insert('atNumber', ['avID' => $row['avID'], 'value' => $row['value']]);
                    }
                });
            }
        }
    }

}
