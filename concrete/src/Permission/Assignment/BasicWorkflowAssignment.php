<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Loader;

class BasicWorkflowAssignment extends Assignment
{
    public function getPermissionAccessObject()
    {
        $db = Loader::db();
        $r = $db->GetOne('select paID from BasicWorkflowPermissionAssignments where wfID = ? and pkID = ?', array(
            $this->getPermissionObject()->getWorkflowID(), $this->pk->getPermissionKeyID(),
        ));

        return Access::getByID($r, $this->pk);
    }

    public function clearPermissionAssignment()
    {
        $db = Loader::db();
        $db->Execute('update BasicWorkflowPermissionAssignments set paID = 0 where pkID = ? and wfID = ?', array($this->pk->getPermissionKeyID(), $this->getPermissionObject()->getWorkflowID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Loader::db();
        $db->Replace('BasicWorkflowPermissionAssignments', array('wfID' => $this->getPermissionObject()->getWorkflowID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('wfID', 'pkID'), true);
        $pa->markAsInUse();
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        return parent::getPermissionKeyToolsURL($task) . '&wfID=' . $this->getPermissionObject()->getWorkflowID();
    }
}
