<?
defined('C5_EXECUTE') or die("Access Denied.");


class BasicWorkflow extends Workflow  {
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from BasicWorkflowPermissionAssignments where wfID = ?', array($this->wfID));
		parent::delete();
	}
	
	public function start() {
		// let's get all the people who are set to be notified on entry
		$nk = PermissionKey::getByHandle('notify_on_entry');
		$nk->setPermissionObject($this);
	}

}