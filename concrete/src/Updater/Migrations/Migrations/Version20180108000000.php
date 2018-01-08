<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180108000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            Version::class,
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
