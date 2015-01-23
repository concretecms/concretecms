<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5732 extends AbstractMigration
{

    public function getName()
    {
        return '20150123000000';
    }

    public function up(Schema $schema)
    {
        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');

        $pkx = Category::getByHandle('multilingual_section');
        if (!is_object($pkx)) {
            $pkx = Category::add('multilingual_section');
        }
        $pkx->associateAccessEntityType(Type::getByHandle('group'));
        $pkx->associateAccessEntityType(Type::getByHandle('user'));
        $pkx->associateAccessEntityType(Type::getByHandle('group_combination'));

        // TODO: Convert database PermissionDuration objects to new class signature.
    }

    public function down(Schema $schema)
    {
    }
}
