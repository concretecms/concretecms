<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160308000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'CollectionVersions',
        ));
    }

    public function down(Schema $schema)
    {
    }
}
