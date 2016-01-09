<?php
namespace Concrete\Core\Permission\Key;

use Concrete\Core\Permission\Access\Access;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use PermissionKey;

class FileKey extends Key
{


    public function validate()
    {
        $u = new User();
        if ($u->isSuperUser()) {
            return true;
        }
        $pae = $this->getPermissionAccessObject();
        if (!is_object($pae)) {
            return false;
        }

        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $valid = false;
        $list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);
        foreach ($list as $l) {
            if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
                $valid = true;
            }
            if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
                $valid = false;
            }
        }
        return $valid;
    }


    public function copyFromFileSetToFile()
    {
        $opa = $this->getPermissionAccessObject();
        $paID = false;
        if (is_object($opa)) {
            $paID = $opa->getPermissionAccessID();
        }

        if ($paID == -1) {
            // this is proceeding from a merged file set assignment (copying from multiple file sets)
            $npa = Access::create($this);
            $ids = $opa->getPermissionAccessIDList();
            foreach($ids as $paID) {
                $pax = Access::getByID($paID, $this);
                $pax->duplicate($npa);
            }
            $paID = $npa->getPermissionAccessID();
        }
        if ($paID) {
            $db = Loader::db();
            $db->Replace(
                'FilePermissionAssignments',
                array(
                    'fID' => $this->permissionObject->getFileID(),
                    'pkID' => $this->getPermissionKeyID(),
                    'paID' => $paID
                ),
                array('fID', 'paID', 'pkID'),
                true
            );

        }
    }


}
