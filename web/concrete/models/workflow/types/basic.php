<?
defined('C5_EXECUTE') or die("Access Denied.");


class BasicWorkflow extends Workflow  {
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from BasicWorkflowPermissionAssignments where wfID = ?', array($this->wfID));
		parent::delete();
	}
	
	public function start(WorkflowProgress $wp) {
		// let's get all the people who are set to be notified on entry
		$nk = PermissionKey::getByHandle('notify_on_basic_workflow_entry');
		$nk->setPermissionObject($this);
		$users = $nk->getCurrentlyActiveUsers();
		$req = $wp->getWorkflowRequestObject();
		
		foreach($users as $ui) {
			$mh = Loader::helper('mail');
			$mh->addParameter('uName', $ui->getUserName());			
			$mh->to($ui->getUserEmail());
			$adminUser = UserInfo::getByID(USER_SUPER_ID);
			$mh->from($adminUser->getUserEmail(),  t('Basic Workflow'));
			if (is_object($req)) {
				$mh->addParameter('description', $req->getWorkflowRequestExternalDescription());
			}
			$mh->load('basic_workflow_notification');
			$mh->sendMail();
			unset($mh);
		}
		
	}

}