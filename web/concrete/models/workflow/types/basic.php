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
		$this->notify($wp, 'notify_on_basic_workflow_entry');
	}

	protected function notify($wp, $permission, $parameters = array()) {
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
			foreach($parameters as $key => $value) {
				$mh->addParameter($key, $value);
			}
			$mh->load('basic_workflow_notification');
			$mh->sendMail();
			unset($mh);
		}
		
	}
	
	public function cancel(WorkflowProgress $wp) {
		if ($this->canApproveBasicWorkflow()) {
			$req = $wp->getWorkflowRequestObject();
			$wpr = $req->runTask('cancel', $wp);
			$wp->delete();
			return $wpr;
		}
	}
	
	public function approve(WorkflowProgress $wp) {
		if ($this->canApproveBasicWorkflow()) {
			$req = $wp->getWorkflowRequestObject();
			$wpr = $req->runTask('approve', $wp);
			$wp->delete();
			return $wpr;
		}
	}
	
	protected function canApproveBasicWorkflow() {
		$pk = PermissionKey::getByHandle('approve_basic_workflow_action');
		$pk->setPermissionObject($this);
		return $pk->validate();
	}
	
	public function getWorkflowProgressActions(WorkflowProgress $wp) {
		$pk = PermissionKey::getByHandle('approve_basic_workflow_action');
		$pk->setPermissionObject($this);
		$buttons = array();
		if ($this->canApproveBasicWorkflow()) {
			$req = $wp->getWorkflowRequestObject();
			$button1 = new WorkflowProgressCancelAction();
	
			$button2 = new WorkflowProgressApprovalAction();
			$button2->setWorkflowProgressActionStyleClass($req->getWorkflowRequestApproveButtonClass());
			$button2->setWorkflowProgressActionLabel($req->getWorkflowRequestApproveButtonText());
	
			$buttons[] = $button1;
			$buttons[] = $button2;
		}
		return $buttons;
	}

}