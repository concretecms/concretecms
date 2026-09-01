<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\Group;

final class Version20260821000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/seo/well_known_files',
            'Well-Known Files',
            [
                'cDescription' => 'View per-site sitemap status and server configuration for multisite installs.',
                'meta_keywords' => 'sitemap, xml, robots, well-known, multisite, nginx, apache',
            ]
        );

        $pk = Key::getByHandle('manage_well_known_files');
        if (!$pk instanceof Key) {
            $pk = Key::add(
                'admin',
                'manage_well_known_files',
                'Manage Well-Known Files',
                "Controls whether a user can view and edit a site's well-known files, such as robots.txt, llms.txt, and security.txt.",
                false,
                false
            );
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = Access::create($pk);
            }
            $adminGroup = Group::getByID(ADMIN_GROUP_ID);
            if ($adminGroup) {
                $pa->addListItem(GroupEntity::getOrCreate($adminGroup));
                $pk->getPermissionAssignmentObject()->assignPermissionAccess($pa);
            }
        }
    }
}
