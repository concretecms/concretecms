<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20190522202749 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $pkx = Category::getByHandle('express_entry');
        if (!is_object($pkx)) {
            $pkx = Category::add('express_entry');
        }
        $pkx->associateAccessEntityType(Type::getByHandle('group'));
        $pkx->associateAccessEntityType(Type::getByHandle('user'));
        $pkx->associateAccessEntityType(Type::getByHandle('group_set'));
        $pkx->associateAccessEntityType(Type::getByHandle('group_combination'));

        $key = Key::getByHandle('view_express_entry');
        if (!$key) {
            Key::add(
                'express_entry', 
                'view_express_entry', 
                'View Express Entry', 
                null, 
                false, 
                false
            );
        }
        $key = Key::getByHandle('edit_express_entry');
        if (!$key) {
            Key::add(
                'express_entry',
                'edit_express_entry',
                'Edit Express Entry',
                null,
                false,
                false
            );
        }
        $key = Key::getByHandle('delete_express_entry');
        if (!$key) {
            Key::add(
                'express_entry',
                'delete_express_entry',
                'Delete Express Entry',
                null,
                false,
                false
            );
        }
    }
}
