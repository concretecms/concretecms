<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class WorkflowRequest extends Object {  
	
	protected $currentWP;
	
	public function __construct($pk) {
		$this->pkID = $pk->getPermissionKeyID();
	}

	public function getWorkflowRequestID() { return $this->wrID;}
	public function getWorkflowRequestPermissionKeyID() {return $this->pkID;}
	public function getWorkflowRequestPermissionKeyObject() {
		return PermissionKey::getByID($this->pkID);
	}
	public function setCurrentWorkflowProgressObject(WorkflowProgress $wp) {
		$this->currentWP = $wp;
	}
	public function getCurrentWorkflowProgressObject() {
		return $this->currentWP;
	}
	
	public static function getByID($wrID) {
		$db = Loader::db();
		$wrObject = $db->getOne('select wrObject from WorkflowRequestObjects where wrID = ?', array($wrID));
		if ($wrObject) {
			$wr = unserialize($wrObject);
			return $wr;
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from WorkflowRequestObjects where wrID = ?', array($this->wrID));
	}
	
	public function save() {
		$db = Loader::db();
		if (!$this->wrID) {
			$wrObject = '';
			$db->Execute('insert into WorkflowRequestObjects (wrObject) values (?)', array($wrObject));
			$this->wrID = $db->Insert_ID();
		}
		$wrObject = serialize($this);
		$db->Execute('update WorkflowRequestObjects set wrObject = ? where wrID = ?', array($wrObject, $this->wrID));
	}

		
	/** 
	 * Triggers a workflow request, queries a permission key to see what workflows are attached to it
	 * and initiates them
	 * @return optional WorkflowProgress
	 */
	protected function trigger($pk) {
		if (!$this->wrID) {
			$this->save();
		}
		
		if (!$pk->canPermissionKeyTriggerWorkflow()) { 
			throw new Exception(t('This permission key cannot start a workflow.'));
		}
		
		$workflows = $pk->getWorkflows();
		if (count($workflows) == 0) {
			$defaultWorkflow = new EmptyWorkflow();
			$wp = $this->addWorkflowProgress($defaultWorkflow);
			return $wp->getWorkflowProgressResponseObject();
		}

		foreach($workflows as $wf) {
			$this->addWorkflowProgress($wf);
		}
	}
	
	abstract public function addWorkflowProgress(Workflow $wf);
	abstract public function getWorkflowRequestDescriptionObject();
	abstract public function getWorkflowRequestStyleClass();
	abstract public function getWorkflowRequestApproveButtonText();
	abstract public function getWorkflowRequestApproveButtonClass();
	
	public function runTask($task, WorkflowProgress $wp) {
		if (method_exists($this, $task)) {
			$wpr = call_user_func_array(array($this, $task), array($wp));
			return $wpr;
		}
	}

}
