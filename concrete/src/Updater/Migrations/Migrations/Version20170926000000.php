<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Exception;

class Version20170926000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->connection->executeQuery("UPDATE btSearch SET postTo_cID = NULL WHERE postTo_cID IS NOT NULL AND postTo_cID = ''");
        foreach ([
            "postTo_cID NOT REGEXP '^[1-9][0-9]*$'",
            'IFNULL(postTo_cID + 0, 0) < 1',
            'IFNULL(CAST(postTo_cID AS SIGNED), 0) < 1',
        ] as $try) {
            try {
                $this->connection->executeQuery('UPDATE btSearch SET postTo_cID = NULL WHERE postTo_cID IS NOT NULL AND ' . $try);
                break;
            } catch (Exception $foo) {
            }
        }
        $this->refreshBlockType('search');
    }
}
