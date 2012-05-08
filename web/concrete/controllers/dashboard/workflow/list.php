<?php
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardWorkflowListController extends DashboardBaseController {
	
	public $helpers = array('form');
	
	public function on_start() {
		parent::on_start();
		$types = array();
		$list = WorkflowType::getList();
		foreach($list as $wl) {
			$types[$wl->getWorkflowTypeID()] = $wl->getWorkflowTypeName();
		}
		$this->set('types', $types);
	}

	public function workflow_updated() {
		$this->set('message', t('Workflow Updated.'));
	}
	
	public function workflow_created() {
		$this->set('message', t('Workflow Created.'));
	}
	
	public function workflow_deleted() {
		$this->set('message', t('Workflow Deleted.'));
	}

	public function delete($wfID, $token = null){
		/*
		try {
			$ak = CollectionAttributeKey::getByID($akID); 
				
			if(!($ak instanceof CollectionAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/pages/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
		*/
	}
	
	public function select_type() {
		$wftID = $this->request('wftID');
		$wt = WorkflowType::getByID($wftID);
		$this->set('type', $wt);
	}
	
	public function add() { }
	
	public function submit_add() {
		if (!Loader::helper('validation/token')->validate('add_workflow')) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		if (!$this->post('wfName')) { 
			$this->error->add(t('You must give the workflow a name.'));
		}
		
		if (!$this->error->has()) { 
			$type = WorkflowType::getByID($this->post('wftID'));
			$wf = Workflow::add($type, $this->post('wfName'));
			$this->redirect('/dashboard/workflow/list/', 'view_detail', $wf->getWorkflowID(), 'workflow_created');
		}
	}
	
	public function view_detail($wfID = false, $message = false) {
		$wf = Workflow::getByID($wfID);
		if (!is_object($wf)) {
			$this->redirect("/dashboard/workflow/list");
		}
		switch($message) {
			case 'workflow_created':
				$this->set('message', t('Workflow created successfully. You may now modify its properties.'));
				break;
		}
		
		$this->set('wf', $wf);
	}
	
	public function edit($wfID = 0) {
		/*
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = CollectionAttributeKey::getByID($akID);
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$e = $cnt->validateKey($this->post());
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$key->update($this->post());
				$this->redirect('/dashboard/pages/attributes/', 'attribute_updated');
			}
		}
		*/
	}
	
}