<?
namespace Concrete\Core\Permission\Key;
class PageTypeKey extends Key {
	
	public function copyFromDefaultsToPageType(PermissionKey $pk) {
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