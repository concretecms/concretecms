<?php
namespace Concrete\Core\Permission\Key;

use Concrete\Core\Permission\Assignment\AreaAssignment;
use Loader;

class AreaKey extends Key
{
    public function copyFromPageToArea()
    {
        $db = Loader::db();
        $paID = $this->getPermissionAccessID();
        if ($paID) {
            $db = Loader::db();
            $db->Replace('AreaPermissionAssignments', array(
                'cID' => $this->permissionObject->getCollectionID(),
                'arHandle' => $this->permissionObject->getAreaHandle(),
                'paID' => $paID,
                'pkID' => $this->getPermissionKeyID(),
                ),
                array('cID', 'arHandle', 'pkID'), true);
        }
    }

    protected function getAreaPermissionAccessObject()
    {
        $targ = $this->getPermissionAssignmentObject();
        if ($targ instanceof AreaAssignment) {
            return $targ->getAreaPermissionAccessObject();
        }

        return $this->getPermissionAccessObject();
    }

    protected function getAreaAccessListItems()
    {
        $obj = $this->getAreaPermissionAccessObject();
        if (!$obj) {
            return [];
        }
        $args = func_get_args();
        switch (count($args)) {
            case 0:
                return $obj->getAccessListItems();
            case 1:
                return $obj->getAccessListItems($args[0]);
            case 2:
                return $obj->getAccessListItems($args[0], $args[1]);
            case 3:
                return $obj->getAccessListItems($args[0], $args[1], $args[2]);
            default:
                return call_user_func_array([$obj, 'getAccessListItems'], $args);
        }
    }
}
