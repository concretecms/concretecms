<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {
	
	protected $multiplePageArray; // bulk operations
	public function setMultiplePageArray($pages) {
		$this->multiplePageArray = $pages;
	}
	
	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from PagePermissionAssignments where cID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObject->getPermissionsCollectionID(), $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('PagePermissionKey', 'PagePermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'PagePermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPermissionObject($page);
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
			'cID' => $this->permissionObject->getCollectionID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'peID', 'pkID'), false);
	}
	
	public function clearAssignments() {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionAssignments where cID = ? and pkID = ?', array($this->permissionObject->getCollectionID(), $this->getPermissionKeyID()));
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionAssignments where cID = ? and peID = ? and pkID = ?', array($this->permissionObject->getCollectionID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		if (isset($this->multiplePageArray)) {
			$cIDStr = '';
			foreach($this->multiplePageArray as $sc) {
				$cIDStr .= '&cID[]=' . $sc->getCollectionID();
			}
			return parent::getPermissionKeyToolsURL($task) . $cIDStr;
		} else {
			return parent::getPermissionKeyToolsURL($task) . '&cID=' . $this->getPermissionObject()->getCollectionID();
		}
	}

	public function clearWorkflows() {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionWorkflows where cID = ? and pkID = ?', array($this->getPermissionObject()->getCollectionID(), $this->getPermissionKeyID()));
	}
	
	public function attachWorkflow(Workflow $wf) {
		$db = Loader::db();
		$db->Replace('PagePermissionWorkflows', array('cID' => $this->getPermissionObject()->getCollectionID(), 'pkID' => $this->getPermissionKeyID(), 'wfID' => $wf->getWorkflowID()), array('cID', 'pkID', 'wfID'), true);
	}
	
	public function getWorkflows() {
		$db = Loader::db();
		$r = $db->Execute('select wfID from PagePermissionWorkflows where cID = ? and pkID = ?', array($this->getPermissionObject()->getPermissionsCollectionID(), $this->getPermissionKeyID()));
		$workflows = array();
		while ($row = $r->FetchRow()) {
			$wf = Workflow::getByID($row['wfID']);
			if (is_object($wf)) {
				$workflows[] = $wf;
			}
		}
		return $workflows;
	}


}

class PagePermissionAssignment extends PermissionAssignment {


}