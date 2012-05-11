<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class PageWorkflowRequest extends WorkflowRequest {  
	
	public function setRequestedPage($c) {
		$this->cID = $c->getCollectionID();
	}
	
	public function getRequestedPageID() {
		return $this->cID;
	}

	public function addWorkflowProgress(Workflow $wf) {
		Loader::model('workflow/progress/categories/page');
		$pwp = PageWorkflowProgress::add($wf, $this);
		return $pwp;
	}

	public function trigger() {
		$page = Page::getByID($this->cID);
		$pk = PermissionKey::getByID($this->pkID);
		$pk->setPermissionObject($page);
		return parent::trigger($pk);
	}

	public function cancel(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}
	
}




