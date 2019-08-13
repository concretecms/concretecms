<?php
namespace Concrete\Core\User\Group\AutomatedGroup;

use Concrete\Core\User\User;
use Concrete\Core\User\Group\GroupAutomationController;

/**
 * @since 5.7.4
 */
class DefaultAutomation extends GroupAutomationController
{
    /**
     * Return true to automatically enter the current ux into the group.
     */
    public function check(User $ux)
    {
        return true;
    }
}
