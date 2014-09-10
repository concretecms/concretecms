<?php
namespace Concrete\Core\Permission\Key;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use \Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;

abstract class WorkflowKey extends Key {

	public function getCurrentlyActiveUsers(WorkflowProgress $wp) {
		$paa = $this->getPermissionAccessObject();
		if (!$paa) {
			return array();
		}
		$paa->setWorkflowProgressObject($wp);
		$included = $this->getAccessListItems(self::ACCESS_TYPE_INCLUDE);
		$excluded = $this->getAccessListItems(self::ACCESS_TYPE_EXCLUDE);
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
