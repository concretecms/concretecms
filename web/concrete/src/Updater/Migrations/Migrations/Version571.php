<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version571 extends AbstractMigration
{

    public function getName()
    {
        return '20140929000000';
    }

    public function up(Schema $schema)
    {
        /** @todo Remove key from Config table */
    }

    public function down(Schema $schema)
    {
    }
}
