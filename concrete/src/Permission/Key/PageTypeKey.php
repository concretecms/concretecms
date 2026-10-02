<?php
namespace Concrete\Core\Permission\Key;

use Loader;

/**
 * @property \Concrete\Core\Page\Type\Type|null $permissionObject
 * @method \Concrete\Core\Page\Type\Type|null getPermissionObject()
 */
class PageTypeKey extends Key
{
    public function copyFromDefaultsToPageType(\Concrete\Core\Permission\Key\Key $pk)
    {
        $db = Loader::db();
        $paID = $pk->getPermissionAccessID();
        if ($paID) {
            $db = Loader::db();
            $db->Replace('PageTypePermissionAssignments', array(
                'ptID' => $this->permissionObject->getPageTypeID(),
                'paID' => $paID,
                'pkID' => $this->getPermissionKeyID(),
                ),
                array('ptID', 'pkID'), true);
        }
    }
}
