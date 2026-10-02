<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20260908000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * The single page whose "View" permission determined who could add users before the
     * add_users permission key existed.
     *
     * @var string
     */
    private const ADD_USER_PAGE_PATH = '/dashboard/users/add';

    public function upgradeDatabase()
    {
        if (Key::getByHandle('add_users') instanceof Key) {
            return;
        }

        $pk = Key::add(
            'user',
            'add_users',
            'Add Users',
            'Controls whether a user can add users.',
            false,
            false
        );
        if (!$pk instanceof Key) {
            return;
        }

        // Seed the new permission with whoever could already reach the Add User dashboard page, so
        // that upgrading doesn't take the ability to add users away from anyone who had it. If we
        // can't work out who that is we leave the key unassigned on purpose: nobody may add users
        // until an administrator grants the permission explicitly.
        $entities = $this->getAddUserPageViewAccessEntities();
        if ($entities === []) {
            $this->output(t('Unable to read the View permission for %s - the Add Users permission has been left unassigned.', self::ADD_USER_PAGE_PATH));

            return;
        }

        $pa = Access::create($pk);
        foreach ($entities as $entity) {
            [$accessEntity, $accessType] = $entity;
            $pa->addListItem($accessEntity, false, $accessType);
        }
        $pk->getPermissionAssignmentObject()->assignPermissionAccess($pa);
    }

    /**
     * Get the access entities assigned to the "View" permission of the Add User dashboard page,
     * paired with the access type (included/excluded) they were assigned with.
     *
     * @return array<array{0: \Concrete\Core\Permission\Access\Entity\Entity, 1: int}>
     */
    private function getAddUserPageViewAccessEntities(): array
    {
        $page = Page::getByPath(self::ADD_USER_PAGE_PATH);
        if (!$page instanceof Page || $page->isError()) {
            return [];
        }
        $viewPage = Key::getByHandle('view_page');
        if (!$viewPage instanceof Key) {
            return [];
        }
        // Key::getByHandle() hands back a shared, request-cached instance, so work on a copy:
        // setting a permission object on the original would leak it into anything else that
        // reads view_page later in this same upgrade request.
        $viewPage = clone $viewPage;
        $viewPage->setPermissionObject($page);
        // The page permission assignment resolves inheritance for us, so this picks up the
        // assignment the page actually inherits rather than requiring one of its own.
        $pa = $viewPage->getPermissionAccessObject();
        if (!$pa instanceof Access) {
            return [];
        }

        $entities = [];
        foreach ($pa->getAccessListItems(Key::ACCESS_TYPE_ALL) as $listItem) {
            $accessEntity = $listItem->getAccessEntityObject();
            if (!$accessEntity) {
                continue;
            }
            $entities[] = [$accessEntity, (int) $listItem->getAccessType()];
        }

        return $entities;
    }
}
