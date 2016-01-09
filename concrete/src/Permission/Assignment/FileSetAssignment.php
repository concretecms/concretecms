<?php

namespace Concrete\Core\Permission\Assignment;

use PermissionAccess;
use Concrete\Core\File\Set\Set;
use Database;

class FileSetAssignment extends Assignment
{
    /**
     * @param Set $fs
     */
    public function setPermissionObject($fs)
    {
        $this->permissionObject = $fs;

        if ($fs->overrideGlobalPermissions()) {
            $this->permissionObjectToCheck = $fs;
        } else {
            $fs = Set::getGlobal();
            $this->permissionObjectToCheck = $fs;
        }
    }

    public function getPermissionAccessObject()
    {
        $db = Database::connection();
        $r = $db->GetOne(
            'select paID from FileSetPermissionAssignments where fsID = ? and pkID = ?',
            array(
                $this->permissionObjectToCheck->getFileSetID(),
                $this->pk->getPermissionKeyID(),
            )
        );

        return PermissionAccess::getByID($r, $this->pk);
    }

    public function clearPermissionAssignment()
    {
        $db = Database::connection();
        $db->Execute('update FileSetPermissionAssignments set paID = 0 where pkID = ? and fsID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getFileSetID()));
    }

    public function assignPermissionAccess(PermissionAccess $pa)
    {
        $db = Database::connection();
        $db->Replace('FileSetPermissionAssignments', array('fsID' => $this->getPermissionObject()->getFileSetID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('fsID', 'pkID'), true);
        $pa->markAsInUse();
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        return parent::getPermissionKeyToolsURL($task) . '&fsID=' . $this->getPermissionObject()->getFileSetID();
    }
}
