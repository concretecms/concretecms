<?
defined('C5_EXECUTE') or die("Access Denied.");
class BlockTypePermissionKey extends PermissionKey {
	
	public function getAssignmentList($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from BlockTypePermissionAssignments where pkID = ? ' . $filterString, array(
 			$this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('PermissionKey', 'PermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'PermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = PermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('BlockTypePermissionAssignments', array(
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from BlockTypePermissionAssignments where peID = ? and pkID = ?', array($pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}

}

class BlockTypePermissionAssignment extends PermissionAssignment {}