<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_ApproveStackPageWorkflowRequest extends ApprovePagePageWorkflowRequest {

	public function approve(WorkflowProgress $wp) {
		$s = Stack::getByID($this->getRequestedPageID());
		$v = CollectionVersion::get($s, $this->cvID);
		$v->approve(false);
		if ($s->getStackName() != $v->getVersionName()) {
			// The stack name has changed so we need to
			// update that for the stack object as well.
			$s->update(array('stackName' => $v->getVersionName()));
		}
		Events::fire('on_page_version_submit_approve', $s);
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $s->getCollectionID());
		return $wpr;
	}

	
}