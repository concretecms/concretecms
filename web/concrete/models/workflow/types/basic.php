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
		$message = t('On %s, user %s submitted the following request: %s', date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded())), $u->getUserName(), $req->getWorkflowRequestDescriptionObject()->getText());
		$this->notify($wp, $message, 'notify_on_basic_workflow_entry');
	}
	
	public function getWorkflowProgressDescription(WorkflowProgress $wp) {
		Loader::model('workflow/types/basic/data');
		$bdw = new BasicWorkflowProgressData($wp);
		$ux = UserInfo::getByID($bdw->getUserStartedID());
		$req = $wp->getWorkflowRequestObject();
		$description = $req->getWorkflowRequestDescriptionObject()->getHTML();
		return t('On %s, user <strong>%s</strong> %s', date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded())), $ux->getUserName(), $description);
	}

	public function getWorkflowProgressStatusDescription(WorkflowProgress $wp) {
		$req = $wp->getWorkflowRequestObject();
		return $req->getWorkflowRequestDescriptionObject()->getShortStatus();
	}

	protected function notify(WorkflowProgress $wp, $message, $permission, $parameters = array()) {
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
			$mh->addParameter('message', $message);
			foreach($parameters as $key => $value) {
				$mh->addParameter($key, $value);
			}
			$mh->load('basic_workflow_notification');
			$mh->sendMail();
			unset($mh);
		}
		
	}
	
	public function cancel(WorkflowProgress $wp) {
		if ($this->canApproveWorkflowProgressObject($wp)) {

			$req = $wp->getWorkflowRequestObject();
			Loader::model('workflow/types/basic/data');
			$bdw = new BasicWorkflowProgressData($wp);
			$u = new User();
			$bdw->markCompleted($u);
			
			$ux = UserInfo::getByID($bdw->getUserCompletedID());

			$message = t("On %s, user %s cancelled the following request: \n\n---\n%s\n---\n\n", date(DATE_APP_GENERIC_MDYT_FULL, strtotime($bdw->getDateCompleted())), $ux->getUserName(), $req->getWorkflowRequestDescriptionObject()->getText());
			$this->notify($wp, $message, 'notify_on_basic_workflow_action');

			$wpr = $req->runTask('cancel', $wp);
			$wp->delete();

			Loader::model('workflow/types/basic/data');
			$bdw = new BasicWorkflowProgressData($wp);
			$bdw->delete();			

			return $wpr;
		}
	}
	
	public function approve(WorkflowProgress $wp) {
		if ($this->canApproveWorkflowProgressObject($wp)) {
			$req = $wp->getWorkflowRequestObject();
			Loader::model('workflow/types/basic/data');
			$bdw = new BasicWorkflowProgressData($wp);
			$u = new User();
			$bdw->markCompleted($u);
			
			$ux = UserInfo::getByID($bdw->getUserCompletedID());

			$message = t("On %s, user %s approved the following request: \n\n---\n%s\n---\n\n", date(DATE_APP_GENERIC_MDYT_FULL, strtotime($bdw->getDateCompleted())), $ux->getUserName(), $req->getWorkflowRequestDescriptionObject()->getText());
			$this->notify($wp, $message, 'notify_on_basic_workflow_action');

			$wpr = $req->runTask('approve', $wp);
			$wp->delete();

			Loader::model('workflow/types/basic/data');
			$bdw = new BasicWorkflowProgressData($wp);
			$bdw->delete();			

			return $wpr;
		}
	}
	
	public function canApproveWorkflowProgressObject(WorkflowProgress $wp) {
		$pk = PermissionKey::getByHandle('approve_basic_workflow_action');
		$pk->setPermissionObject($this);
		return $pk->validate();
	}
	
	public function getWorkflowProgressActions(WorkflowProgress $wp) {
		$pk = PermissionKey::getByHandle('approve_basic_workflow_action');
		$pk->setPermissionObject($this);
		$buttons = array();
		if ($this->canApproveWorkflowProgressObject($wp)) {
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