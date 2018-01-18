<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20170926000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->connection->executeQuery('UPDATE btSearch SET postTo_cID = NULL WHERE postTo_cID IS NOT NULL AND IFNULL(postTo_cID + 0, 0) < 1');
        $this->refreshBlockType('search');
    }
}
