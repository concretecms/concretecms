<?php
namespace Concrete\Core\Permission\Key;

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
}
