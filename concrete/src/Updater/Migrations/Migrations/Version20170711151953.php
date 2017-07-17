<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170711151953 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->refreshDatabaseTables([
            'FileImageThumbnailPaths'
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
