<?php
namespace Concrete\Core\Permission\Key;
use Loader;
class PageTypeKey extends Key {

	public function copyFromDefaultsToPageType(\Concrete\Core\Permission\Key\Key $pk) {
		$db = Loader::db();
		$paID = $pk->getPermissionAccessID();
		if ($paID) {
			$db = Loader::db();
			$db->Replace('PageTypePermissionAssignments', array(
				'ptID' => $this->permissionObject->getPageTypeID(),
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('ptID', 'pkID'), true);
		}
	}


}
