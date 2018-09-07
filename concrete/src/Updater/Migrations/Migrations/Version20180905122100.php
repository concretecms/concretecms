<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180905122100 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->addPermissionKey('area', 'add_subarea', 'Add Sub-Areas', 'Can add an area beneath the current area.', false, false);
        $this->addPermissionKey('area', 'approve_area_versions', 'Approve Area Versions', 'Can publish an unapproved version of the area.', false, true);
        $this->addPermissionKey('area', 'delete_area', 'Delete Area', 'Ability to delete this area', false, true);
        $this->addPermissionKey('area', 'delete_area_versions', 'Delete Area Versions', 'Ability to remove old versions of this area.', false, true);
        $this->addPermissionKey('area', 'view_area_versions', 'View Area Versions', 'Can view the area versions dialog and read past versions of an area.', false, false);
        $this->addPermissionKey('area', 'move_or_copy_area', 'Move or Copy Area', 'Can move or copy this area to another location.', false, true);
        $this->addPermissionKey('area', 'edit_area_properties', 'Edit Properties', 'Ability to change anything in the Area Properties menu.', false, false);
    }

    /**
     * @param string $category
     * @param string $handle
     * @param string $handle
     * @param string $name
     * @param string $description
     * @param bool $hasCustomClass
     * @param bool $canTriggerWorkflow
     *
     * @return \Concrete\Core\Permission\Key\Key|null returns the newly added permission key (or NULL if the key already existed)
     */
    protected function addPermissionKey($category, $handle, $name, $description, $hasCustomClass, $canTriggerWorkflow)
    {
        $pk = Key::getByHandle($handle);
        if ($pk !== null) {
            return null;
        }
        $this->output("Adding permission key {$handle}...");
        $pkc = Category::getByHandle($category);

        return call_user_func(
            [$pkc->getPermissionKeyClass(), 'add'],
            $category,
            $handle,
            $name,
            $description,
            $canTriggerWorkflow,
            $hasCustomClass
        );
    }
}
