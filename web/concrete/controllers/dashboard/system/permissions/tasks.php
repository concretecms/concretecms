<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsTasksController extends DashboardBaseController {
	
	public function view() {
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dashboard.permissions.js'));	
		$this->token = Loader::helper('validation/token');
	}
	
	public function task_permissions_saved() {
		$this->view();
		$this->set('message', t('Permissions saved'));
	}

	public function save_task_permissions() {
		if (!$this->token->validate("update_permissions")) {
			$this->set('error', array($this->token->getErrorMessage()));
			return;
		}	
		
		$tp = new TaskPermission();
		if (!$tp->canAccessTaskPermissions()) {
			$this->set('error', array(t('You do not have permission to modify these items.')));
			return;
		}
		
		$post = $this->post();
		
		$h = Loader::helper('concrete/dashboard/task_permissions');
		$h->save($post);
		$this->redirect('/dashboard/system/permissions/tasks', 'task_permissions_saved');
	
	}
	

}