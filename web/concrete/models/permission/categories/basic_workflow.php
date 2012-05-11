<?
defined('C5_EXECUTE') or die("Access Denied.");
class BasicWorkflowPermissionKey extends PermissionKey {
	
	public function getAssignmentList($accessType = BasicWorkflowPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from BasicWorkflowPermissionAssignments where wfID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObject->getWorkflowID(), $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('BasicWorkflowPermissionKey', 'BasicWorkflowPermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'BasicWorkflowPermissionAssignment';
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
	
	public function getCurrentlyActiveUsers() {
		$included = $this->getAssignmentList(PermissionKey::ACCESS_TYPE_INCLUDE);
		$excluded = $this->getAssignmentList(PermissionKey::ACCESS_TYPE_EXCLUDE);
		$included = PermissionDuration::filterByActive($included);
		$excluded = PermissionDuration::filterByActive($excluded);
		$users = array();
		$usersExcluded = array();
		foreach($included as $inc) {
			$pae = $inc->getAccessEntityObject();
			$users = array_merge($users, $pae->getAccessEntityUsers());	
		}
		$users = array_unique($users);

		foreach($excluded as $inc) {
			$pae = $inc->getAccessEntityObject();
			$usersExcluded = array_merge($usersExcluded, $pae->getAccessEntityUsers());	
		}
		$users = array_diff($users, $usersExcluded);
		return $users;	
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = BasicWorkflowPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		print_r($this->permissionObject);
		$db->Replace('BasicWorkflowPermissionAssignments', array(
			'wfID' => $this->permissionObject->getWorkflowID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('wfID', 'peID', 'pkID'), false);
	}
	
	public function clearAssignments() {
		$db = Loader::db();
		$db->Execute('delete from BasicWorkflowPermissionAssignments where wfID = ? and pkID = ?', array($this->permissionObject->getWorkflowID(), $this->getPermissionKeyID()));
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from BasicWorkflowPermissionAssignments where wfID = ? and peID = ? and pkID = ?', array($this->permissionObject->getWorkflowID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&wfID=' . $this->getPermissionObject()->getWorkflowID();
	}

	/** 
	 * No workflow functionality in workflows.
	 * @private
	 */
	public function clearWorkflows() {}
	
	/** 
	 * @private
	 */
	public function attachWorkflow(Workflow $wf) {}

	/** 
	 * @private
	 */
	public function getWorkflows() {return array();}

}

class BasicWorkflowPermissionAssignment extends PermissionAssignment {}