<?
defined('C5_EXECUTE') or die("Access Denied.");
class FilePermissionKey extends PermissionKey {

	public function getAssignmentList($accessType = FilePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from FilePermissionAssignments where fID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObject->getFileID(), $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('FilePermissionKey', 'FilePermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'FilePermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPermissionObject($this->permissionObject);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = FilePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('FilePermissionAssignments', array(
			'fID' => $this->permissionObject->getFileID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('fID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from FilePermissionAssignments where fID = ? and peID = ? and pkID = ?', array($this->permissionObject->getFileID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fID=' . $this->getPermissionObject()->getFileID();
	}

}

class FilePermissionAssignment extends PermissionAssignment {


}