<?php
namespace Concrete\Core\Permission\Key;

use Loader;

class GatheringKey extends Key
{
    public function copyFromDefaultsToGathering(PermissionKey $pk)
    {
        $db = Loader::db();
        $paID = $pk->getPermissionAccessID();
        if ($paID) {
            $db = Loader::db();
            $db->Replace('GatheringPermissionAssignments', array(
                'gaID' => $this->permissionObject->getGatheringID(),
                'paID' => $paID,
                'pkID' => $this->getPermissionKeyID(),
                ),
                array('gaID', 'pkID'), true);
        }
    }
}
