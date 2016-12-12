<?php

namespace Concrete\Core\User\Group\AutomatedGroup;
use Concrete\Core\User\User;
use Concrete\Core\User\Group\GroupAutomationController;

class DefaultAutomation extends GroupAutomationController {

    /**
     * Return true to automatically enter the current ux into the group
     */
    public function check(User $ux) {
        return true;
    }

}