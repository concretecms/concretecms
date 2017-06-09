<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170609000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\Type',
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
