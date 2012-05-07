<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class WorkflowRequestAction extends Object {  
	
	protected $wrActionStyleClass = '';
	protected $wrActionTask = '';
	
	public function setWorkflowRequestActionStyleClass($class) {
		$this->wrActionStyleClass = $class;
	}
	public function setWorkflowRequestActionLabel($label) {
		$this->wrActionLabel = $label;
	}	
	public function setWorkflowRequestActionTask($wrActionTask) {
		$this->wrActionTask = $wrActionTask;
	}	
	
	public function getWorkflowRequestActionStyleClass() {
		return $this->wrActionStyleClass;
	}
	public function getWorkflowRequestActionLabel() {
		return $this->wrActionLabel;
	}
	public function getWorkflowRequestActionTask() {
		return $this->wrActionTask;
	}

}
