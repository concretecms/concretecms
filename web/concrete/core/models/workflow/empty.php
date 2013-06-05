<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_EmptyWorkflow extends Workflow {
	public function start(WorkflowProgress $wp) {
		$req = $wp->getWorkflowRequestObject();
		$wpr = $req->approve($wp);
		$wp->delete();
		return $wpr;
	}
	public function updateDetails($vars) {}
	public function loadDetails() {}
	
	public function canApproveWorkflowProgressObject(WorkflowProgress $wp) {
		return false;
	}
	public function getWorkflowProgressActions(WorkflowProgress $wp) {
		return array();
	}
	public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp) {
		return '';
	}
	public function getWorkflowProgressStatusDescription(WorkflowProgress $wp) {
		return '';
	}

}
