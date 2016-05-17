<?php
namespace Concrete\Core\Permission\Key;

use Loader;

class BlockKey extends Key
{
    public function copyFromPageOrAreaToBlock()
    {
        $paID = $this->getPermissionAccessID();
        if ($paID) {
            $db = Loader::db();
            $co = $this->permissionObject->getBlockCollectionObject();
            $arHandle = $this->permissionObject->getAreaHandle();
            $db->Replace('BlockPermissionAssignments', array(
                'cID' => $co->getCollectionID(),
                'cvID' => $co->getVersionID(),
                'bID' => $this->permissionObject->getBlockID(),
                'pkID' => $this->getPermissionKeyID(),
                'paID' => $paID, ), array('cID', 'cvID', 'bID', 'paID', 'pkID'), true);
        }
    }
}
