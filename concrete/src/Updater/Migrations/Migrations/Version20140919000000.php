<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20140919000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.0.2';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // assign the page owner access entity to the page type category
        $pe = Type::getByHandle('page_owner');
        $category = Category::getByHandle('page_type');
        if (is_object($category) && is_object($pe)) {
            $category->associateAccessEntityType($pe);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::downgradeDatabase()
     */
    public function downgradeDatabase()
    {
        $pe = Type::getByHandle('page_owner');
        $category = Category::getByHandle('page_type');
        if (is_object($category) && is_object($pe)) {
            $category->deassociateAccessEntityType($pe);
        }
    }
}
