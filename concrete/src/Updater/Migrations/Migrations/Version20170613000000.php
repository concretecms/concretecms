<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20170613000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_form');
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        if ($folder) {
            $this->connection->executeQuery(
                'update btExpressForm set addFilesToFolder = ?', [$folder->getTreeNodeID()]
            );
        }
    }
}
