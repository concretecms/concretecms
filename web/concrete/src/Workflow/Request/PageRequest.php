<?php
namespace Concrete\Core\Workflow\Request;
use Loader;
use Page;
use \Concrete\Core\Workflow\Description as WorkflowDescription;
use Permissions;
use PermissionKey;
use \Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use CollectionVersion;
use Events;
use \Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;
use \Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use \Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;

abstract class PageRequest extends Request {

	public function setRequestedPage($c) {
		$this->cID = $c->getCollectionID();
	}

	public function getRequestedPageID() {
		return $this->cID;
	}

	public function getRequestedPageVersionID() {
		if (isset($this->cvID)) {
			return $cvID;
		}
		$c = Page::getByID($this->cID, 'RECENT');
		return $c->getVersionID();
	}

	public function setRequestedPageVersionID($cvID) {
		$this->cvID = $cvID;
	}

	public function addWorkflowProgress(\Concrete\Core\Workflow\Workflow $wf) {

		$pwp = PageWorkflowProgress::add($wf, $this);
		$r = $pwp->start();
		$pwp->setWorkflowProgressResponseObject($r);
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




