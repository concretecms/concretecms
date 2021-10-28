<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Permission\Checker;

class BoardInstanceSlotRuleResponse extends Response
{

    public function canDeleteBoardInstanceSlotRule(): bool
    {
        $permissionMethod = 'canEditBoardContents';
        if ($this->getPermissionObject()->isLocked()) {
            $permissionMethod = 'canEditBoardLockedRules'; // One locked rule is all it takes.
        }
        $boardPermissions = new Checker($this->getPermissionObject()->getInstance()->getBoard());
        return $boardPermissions->$permissionMethod();
    }


}
