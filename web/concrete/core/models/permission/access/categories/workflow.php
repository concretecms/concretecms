<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_WorkflowPermissionAccess extends PermissionAccess {
	
	public function setWorkflowProgressObject(WorkflowProgress $wp) {
		$this->wp = $wp;
	}
	
	public function getWorkflowProgressObject() {
		return $this->wp;
	}
	
}