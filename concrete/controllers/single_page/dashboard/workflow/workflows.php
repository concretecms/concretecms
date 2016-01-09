<?php
namespace Concrete\Controller\SinglePage\Dashboard\Workflow;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Workflow;
use Loader;
use Exception;
use \Concrete\Core\Workflow\Type as WorkflowType;

class Workflows extends DashboardPageController {
	
	public $helpers = array('form');
	

	public function delete($wfID = null, $token = null){
		try {
			$wf = Workflow::getByID($wfID); 
				
			if(!($wf instanceof Workflow)) {
				throw new Exception(t('Invalid workflow ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_workflow', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$wf->delete();
			
			$this->redirect("/dashboard/workflow/workflows", 'workflow_deleted');
		} catch (Exception $e) {
			$this->error->add($e);
		}
		$this->view();
	}
	
	public function save_workflow_details() {
		if (!Loader::helper('validation/token')->validate('save_workflow_details')) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$wfName = trim($this->post('wfName'));
		if (!$wfName) { 
			$this->error->add(t('You must give the workflow a name.'));
		} else if (preg_match('/[<>{};?"`]/i', $wfName)) {
			$this->error->add(t('Invalid characters in workflow name.'));
		}
		if (!$this->error->has()) {
			$wf = Workflow::getByID($this->post('wfID'));
			$wf->updateName($wfName);
			$wf->updateDetails($this->post());
			$this->redirect('/dashboard/workflow/workflows', 'view_detail', $this->post('wfID'), 'workflow_updated');
		} else {
			$this->view_detail($this->post('wfID'));
		}		
	}
	
	public function view() {
		$workflows = Workflow::getList();
		$this->set('workflows', $workflows);
	}
	
	public function add() {
		$types = array();
		$list = WorkflowType::getList();
		foreach($list as $wl) {
			$types[$wl->getWorkflowTypeID()] = $wl->getWorkflowTypeName();
		}
		$this->set('types', $types);
		$this->set('typeObjects', $list);
	 }
	
	public function workflow_deleted() {
		$this->set("message", t('Workflow deleted successfully.'));
		$this->view();
	}
	
	public function submit_add() {
		if (!Loader::helper('validation/token')->validate('add_workflow')) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$wfName = trim($this->post('wfName'));
		if (!$wfName) { 
			$this->error->add(t('You must give the workflow a name.'));
		} else if (preg_match('/[<>{};?"`]/i', $wfName)) {
			$this->error->add(t('Invalid characters in workflow name.'));
		}
		$db = Loader::db();
		$wfID = $db->getOne('SELECT wfID FROM Workflows WHERE wfName=?',array($wfName));
		if ($wfID) {
			$this->error->add(t('Workflow with that name already exists.'));
		}
		if (!$this->error->has()) { 
			$type = WorkflowType::getByID($this->post('wftID'));
			if (!is_object($type) || !($type instanceof WorkflowType)) {
				$this->error->add(t('Invalid Workflow Type.'));
				$this->add();
				return;
			}
			$wf = Workflow::add($type, $wfName);
			$wf->updateDetails($this->post());
			$this->redirect('/dashboard/workflow/workflows/', 'view_detail', $wf->getWorkflowID(), 'workflow_created');
		}
		$this->add();
	}
	
	public function edit_details($wfID = false) {
		$wf = Workflow::getByID($wfID);
		if (!is_object($wf)) {
			$this->redirect("/dashboard/workflow/workflows");
		}
		$this->set('wf', $wf);
	}

	
	public function view_detail($wfID = false, $message = false) {
		$wf = Workflow::getByID($wfID);
		if (!is_object($wf)) {
			$this->redirect("/dashboard/workflow/workflows");
		}
		switch($message) {
			case 'workflow_created':
				$this->set('message', t('Workflow created successfully. You may now modify its properties.'));
				break;
			case 'workflow_updated':
				$this->set('message', t('Workflow updated.'));
				break;
		}
		
		$this->set('wf', $wf);
	}
	
}