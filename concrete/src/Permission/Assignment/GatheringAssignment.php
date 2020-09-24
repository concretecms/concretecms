<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Loader;

class GatheringAssignment extends Assignment
{
    public function getPermissionAccessObject()
    {
        $db = Loader::db();
        $r = $db->GetOne('select paID from GatheringPermissionAssignments where gaID = ? and pkID = ?', array(
            $this->permissionObject->getGatheringID(), $this->pk->getPermissionKeyID(),
        ));

        return Access::getByID($r, $this->pk);
    }

    public function clearPermissionAssignment()
    {
        $db = Loader::db();
        $db->Execute('update GatheringPermissionAssignments set paID = 0 where pkID = ? and gaID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getGatheringID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Loader::db();
        $db->Replace('GatheringPermissionAssignments', array('gaID' => $this->getPermissionObject()->getGatheringID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('gaID', 'pkID'), true);
        $pa->markAsInUse();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::getPermissionKeyTaskURL()
     */
    public function getPermissionKeyTaskURL(string $task = '', array $options = []): string
    {
        return parent::getPermissionKeyTaskURL($task, $options + ['gaID' => $this->getPermissionObject()->getGatheringID()]);
    }
}
