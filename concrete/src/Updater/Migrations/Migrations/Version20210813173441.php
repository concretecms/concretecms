<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210813173441 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $key = Key::getByHandle('add_group_folder');
        if (!$key) {
            $key = Key::add('group_folder', 'add_group_folder', t("Add Group Folder"), t("Add Group Folder"), false, false);
        }
        $key = Key::getByHandle('assign_groups');
        if (!$key) {
            $key = Key::add('group_folder', 'assign_groups', t("Assign Groups"), t("Can assign the groups within this folder."), false, false);
        }

    }

}
