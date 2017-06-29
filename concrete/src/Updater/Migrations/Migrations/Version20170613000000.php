<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170613000000 extends AbstractMigration
{
    public function up(Schema $schema)
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

    public function down(Schema $schema)
    {
    }
}
