<?
defined('C5_EXECUTE') or die("Access Denied.");


class BasicWorkflow extends Workflow  {
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from BasicWorkflowPermissionAssignments where wfID = ?', array($this->wfID));
		parent::delete();
	}
	
	public function start(WorkflowProgress $wp) {
		// lets save the basic data associated with this workflow. 
		$u = new User();
		$db = Loader::db();
		$db->Execute('insert into BasicWorkflowProgressData (wpID, uIDStarted) values (?, ?)', array($wp->getWorkflowProgressID(), $u->getUserID()));
		
		// let's get all the people who are set to be notified on entry
		$req = $wp->getWorkflowRequestObject();
		$d = new WorkflowDescription();
		$d->setText(t('User %s %s on %s', $u->getUserName(), $req->getWorkflowRequestDescription(), date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded()))));
		$this->notify($wp, $d, 'notify_on_basic_workflow_entry');
	}
	
	public function getWorkflowProgressDescriptionObject(WorkflowProgress $wp) {
		Loader::model('workflow/types/basic/data');
		$bdw = new BasicWorkflowProgressData($wp);
		$ux = UserInfo::getByID($bdw->getUserStartedID());
		$d = new WorkflowDescription();
		$d->setHTML(t('User <strong>%s</strong> marked this page for deletion on %s', $ux->getUserName(), date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded()))));
		return $d;
	}

	protected function notify(WorkflowProgress $wp, WorkflowDescription $d, $permission, $parameters = array()) {
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
			$mh->addParameter('description', $d->getText());
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