<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @since 8.2.0
 */
class Version20170613000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     * @since 8.3.2
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_form');
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        if ($folder) {
            $this->connection->executeQuery(
                'update btExpressForm set addFilesToFolder = ? where addFilesToFolder IS NULL or addFilesToFolder = 0', [$folder->getTreeNodeID()]
            );
        }
    }
}
