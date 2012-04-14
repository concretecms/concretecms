<?
defined('C5_EXECUTE') or die("Access Denied.");
class TaskPermissionKey extends PermissionKey {
	
	public function getAssignmentList($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from TaskPermissionAssignments where pkID = ? ' . $filterString, array(
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
		
		$db->Replace('TaskPermissionAssignments', array(
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from TaskPermissionAssignments where peID = ? and pkID = ?', array($pe->getAccessEntityID(), $this->getPermissionKeyID()));	
	}
	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from TaskPermissionAssignments where pkID = ?', array($this->getPermissionKeyID()));
	}
	
	public function exportAccess($pxml) {
		$assignments = $this->getAssignmentList();
		if (count($assignments) > 0) { 
			$access = $pxml->addChild('access');
			foreach($assignments as $as) { 
				$ase = $as->getAccessEntityObject();
				switch($ase->getAccessEntityType()) {
					case 'G':
						$g = $ase->getGroupObject();
						$node = $access->addChild('group');
						$node->addAttribute('name', $g->getGroupName());
						break;
					case 'U':
						$g = $ase->getUserObject();
						$node = $access->addChild('user');
						$node->addAttribute('name', $ui->getUserName());
						break;
				}
			}
		}
	}

}

/**
 * legacy
 */
class TaskPermission extends Permissions {
	
	public function getByHandle($handle) {
		$pk = PermissionKey::getByHandle($handle);
		return $pk;
	}
	
}