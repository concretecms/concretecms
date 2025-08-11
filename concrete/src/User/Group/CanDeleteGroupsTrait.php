<?php

declare(strict_types=1);

namespace Concrete\Core\User\Group;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\User\User;

trait CanDeleteGroupsTrait
{
    /**
     * Can the current user delete groups?
     *
     * @return bool
     */
    protected function userCanDeleteGroups(): bool
    {
        return $this->getWhyUserCantDeleteGroups() === '';
    }

    /**
     * Get the reason why the current user can't delete groups.
     *
     * @return string Empty string if the current user CAN delete groups
     */
    protected function getWhyUserCantDeleteGroups(): string
    {
        $app = app();
        $config = $app->make(Repository::class);
        if ($config->get('concrete.user.group.delete_requires_superuser')) {
            if (!$app->make(User::class)->isSuperUser()) {
                return t('You need to be a super user to delete groups.');
            }
        }

        return '';
    }
}
