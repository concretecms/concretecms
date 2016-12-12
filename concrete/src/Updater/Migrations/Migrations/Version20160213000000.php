<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160213000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // added new delimiter settings.
        $bt = \BlockType::getByHandle('page_attribute_display');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }
}
