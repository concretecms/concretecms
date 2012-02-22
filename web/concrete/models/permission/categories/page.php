<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {
	
	protected $page;
	
	public function getPageObject() {
		return $this->page;
	}
	
	public function setPageObject(Page $page) {
		$this->page = $page;
	}

	public static function getByID($pkID, Page $page) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			$pk->setPageObject($page);
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
 		$r = $db->Execute('select peID, pdID from PagePermissionAssignments where cID = ? and accessType = ? and pkID = ?', array(
 			$this->page->getPermissionsCollectionID(), $accessType, $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('PagePermissionKey', 'PagePermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'PagePermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($accessType);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPageObject($page);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('PagePermissionAssignments', array(
			'cID' => $this->page->getCollectionID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionAssignments where cID = ? and peID = ?', array($this->page->getCollectionID(), $pe->getAccessEntityID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $this->getPageObject()->getCollectionID();
	}

}

class PagePermissionAssignment extends PermissionAssignment {

	public function setPageObject($page) {
		$this->page = $page;
	}


}