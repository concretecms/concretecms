<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use \Concrete\Core\Conversation\FlagType\FlagType;


class Version5722 extends AbstractMigration
{

    public function getName()
    {
        return '20141121000000';
    }

    public function up(Schema $schema)
    {
        $ft = FlagType::getByhandle('spam');
        if (!is_object($ft)) {
            FlagType::add('spam');
        }
    }

    public function down(Schema $schema)
    {
    }
}
