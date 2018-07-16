<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\Group;
use Doctrine\DBAL\Schema\Schema;

class Version20180403143200 extends AbstractMigration implements RepeatableMigrationInterface
{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {

        $pk = Key::getByHandle('edit_topic_tree');
        if (!$pk instanceof Key) {
            $pk = Key::add('admin', 'edit_topic_tree', 'Edit Topic Tree', 'Controls whether a user can edit a topic tree name.',
                false, false);
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = Access::create($pk);
            }
            $adminGroup = Group::getByID(ADMIN_GROUP_ID);
            if ($adminGroup) {
                $adminGroupEntity = GroupEntity::getOrCreate($adminGroup);
                $pa->addListItem($adminGroupEntity);
                $pt = $pk->getPermissionAssignmentObject();
                $pt->assignPermissionAccess($pa);
            }
        }


    }

}
