<?
defined('C5_EXECUTE') or die("Access Denied.");
class FileSetPermissionKey extends PermissionKey {

	const ACCESS_TYPE_MINE = 5;
	protected $permissionObjectToCheck;
	
	public function getSupportedAccessTypes() {
		$types = array(
			self::ACCESS_TYPE_INCLUDE => t('Included'),
			self::ACCESS_TYPE_MINE => t('Mine'),
			self::ACCESS_TYPE_EXCLUDE => t('Excluded'),
		);
		return $types;
	}

	public function setPermissionObject(FileSet $fs) {
		$this->permissionObject = $fs;
		
		if ($fs->overrideGlobalPermissions()) {
			$this->permissionObjectToCheck = $fs;
		} else {
			$fs = FileSet::getGlobal();
			$this->permissionObjectToCheck = $fs;
		}
	}

	public function validate() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$accessEntities = $u->getUserAccessEntityObjects();
		$valid = false;
		$list = $this->getAssignmentList(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$valid = true;
			}
			if ($l->getAccessType() == FileSetPermissionKey::ACCESS_TYPE_MINE) {
				$valid = true;
			}
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
				$valid = false;
			}
		}
		return $valid;		
	}
	
	public function getAssignmentList($accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from FileSetPermissionAssignments where fsID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObjectToCheck->getFileSetID(), $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('FileSetPermissionKey', 'FileSetPermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'FileSetPermissionAssignment';
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
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('FileSetPermissionAssignments', array(
			'fsID' => $this->permissionObject->getFileSetID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('fsID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from FileSetPermissionAssignments where fsID = ? and peID = ? and pkID = ?', array($this->permissionObject->getFileSetID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fsID=' . $this->getPermissionObject()->getFileSetID();
	}

}

class FileSetPermissionAssignment extends PermissionAssignment {


}

/**
 * legacy
 */
class FilePermissions {

	public static function getGlobal() {
		$fs = FileSet::getGlobal();
		$fsp = new Permissions($fs);
		return $fsp;
	}
}