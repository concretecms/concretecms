<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class WorkflowPermissionKey extends PermissionKey {

	public function getCurrentlyActiveUsers(WorkflowProgress $wp) {
		$paa = $this->getPermissionAccessObject();
		$paa->setWorkflowProgressObject($wp);
		if (!$paa) {
			return array();
		}
		$included = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE);
		$excluded = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE);
		$included = PermissionDuration::filterByActive($included);
		$excluded = PermissionDuration::filterByActive($excluded);
		$users = array();
		$usersExcluded = array();
		foreach($included as $inc) {
			$pae = $inc->getAccessEntityObject();
			$users = array_merge($users, $pae->getAccessEntityUsers($paa));	
		}
		$users = array_unique($users);

		foreach($excluded as $inc) {
			$pae = $inc->getAccessEntityObject();
			$usersExcluded = array_merge($usersExcluded, $pae->getAccessEntityUsers());	
		}
		$users = array_diff($users, $usersExcluded);
		return $users;	
	}

}

class WorkflowPermissionAccess extends PermissionAccess {
	
	public function setWorkflowProgressObject(WorkflowProgress $wp) {
		$this->wp = $wp;
	}
	
	public function getWorkflowProgressObject() {
		return $this->wp;
	}
	
}