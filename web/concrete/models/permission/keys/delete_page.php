<?
defined('C5_EXECUTE') or die("Access Denied.");

class DeletePagePagePermissionKey extends PagePermissionKey  {

	public function getWorkflowRequestObject($c) {
		$dpp = new DeletePagePageWorkflowRequest($this);
		$dpp->setTargetPage($c);
		$dpp->save();
		return $dpp; 
	}

}

class DeletePagePageWorkflowRequest extends WorkflowRequest {
	
	public function setTargetPage($c) {
		$this->targetCID = $c->getCollectionID();
	}
	
}