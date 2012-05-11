<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class WorkflowProgressAction extends Object {  
	
	protected $wrActionStyleClass = '';
	protected $wrActionTask = '';
	
	public function setWorkflowProgressActionStyleClass($class) {
		$this->wrActionStyleClass = $class;
	}
	public function setWorkflowProgressActionLabel($label) {
		$this->wrActionLabel = $label;
	}	
	public function setWorkflowProgressActionTask($wrActionTask) {
		$this->wrActionTask = $wrActionTask;
	}	
	
	public function getWorkflowProgressActionStyleClass() {
		return $this->wrActionStyleClass;
	}
	public function getWorkflowProgressActionLabel() {
		return $this->wrActionLabel;
	}
	public function getWorkflowProgressActionTask() {
		return $this->wrActionTask;
	}

}

class WorkflowProgressCancelAction extends WorkflowProgressAction {
	
	public function __construct() {
		$this->setWorkflowProgressActionLabel(t('Cancel'));
		$this->setWorkflowProgressActionTask('cancel');
	}
	
}

class WorkflowProgressApprovalAction extends WorkflowProgressAction {
	
	public function __construct() {
		$this->setWorkflowProgressActionLabel(t('Approve'));
		$this->setWorkflowProgressActionTask('approve');
	}
	
}