<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140919000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.0.2';
    }

    public function up(Schema $schema)
    {
        // assign the page owner access entity to the page type category
        $pe = Type::getByHandle('page_owner');
        $category = Category::getByHandle('page_type');
        if (is_object($category) && is_object($pe)) {
            $category->associateAccessEntityType($pe);
        }
    }

    public function down(Schema $schema)
    {
        $pe = Type::getByHandle('page_owner');
        $category = Category::getByHandle('page_type');
        if (is_object($category) && is_object($pe)) {
            $category->deassociateAccessEntityType($pe);
        }
    }
}
