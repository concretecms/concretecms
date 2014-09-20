<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Access\Entity\PageOwnerEntity;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5702 extends AbstractMigration
{
    public function getName()
    {
        return '20140919000000';
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