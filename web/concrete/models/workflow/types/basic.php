<?
defined('C5_EXECUTE') or die("Access Denied.");


class BasicWorkflowType extends WorkflowType  {
	
	public function addAssignment(Workflow $wf, PermissionAccessEntity $pae, $durationObject = false) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		$db->Replace('BasicWorkflowAssignments', array(
			'wfID' => $wf->getWorkflowID(),
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID
		), array('wfID', 'peID'), false);
	}

}