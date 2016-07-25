<?php
namespace Concrete\Core\Permission\Key;

use Core;

class ConversationKey extends Key
{
    protected $permissionObjectToCheck;

    // We need this because we don't always have a permission object
    public function getPermissionAssignmentObject()
    {
        $targ = Core::make('\Concrete\Core\Permission\Assignment\ConversationAssignment');
        if (is_object($this->permissionObject)) {
            $targ->setPermissionObject($this->permissionObject);
        }
        $targ->setPermissionKeyObject($this);

        return $targ;
    }
}
