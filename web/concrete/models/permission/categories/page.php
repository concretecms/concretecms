<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {
	
	public static function getByID($pkID) {
		$pk = new self;
		$pk->load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}
	
	public function getAssignmentList($page, $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
 		$r = $db->Execute('select peID, pdID from PagePermissionAssignments where cID = ? and accessType = ? and pkID = ?', array(
 			$page->getCollectionID(), $accessType, $this->getPermissionKeyID()
 		));
 		$list = array();
 		while ($row = $r->FetchRow()) {
 			$ppa = new PagePermissionAssignment();
 			$ppa->setAccessType($accessType);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPageObject($page);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment($page, PermissionAccessEntity $pae, $durationObject = false, $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('PagePermissionAssignments', array(
			'cID' => $page->getCollectionID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment($page, PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionAssignments where cID = ? and peID = ?', array($page->getCollectionID(), $pe->getAccessEntityID()));
		
	}

}

class PagePermissionAssignment extends PermissionAssignment {

	public function setPageObject($page) {
		$this->page = $page;
	}


}