<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version574 extends AbstractMigration
{

    public function getName()
    {
        return '20141229000000';
    }

    public function up(Schema $schema)
    {
        // TODO: Convert database PermissionDuration objects to new class signature.
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }

}
