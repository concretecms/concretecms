<?php
namespace Concrete\Core\User\Group\AutomatedGroup;

use Concrete\Core\User\Group\GroupAutomationController;
use Concrete\Core\User\User;

class TestGroup extends GroupAutomationController
{
    public function check(User $ux)
    {
        if (preg_match('/a|e|i|o|u/i', $ux->getUsername())) {
            return true;
        }

        return false;
    }
}
