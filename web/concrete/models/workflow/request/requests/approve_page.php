<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class ApprovePagePageWorkflowRequest extends PageWorkflowRequest {
	
	const REQUEST_STATUS_NUM = 30;

	public function __construct() {
		$pk = PermissionKey::getByHandle('approve_page_versions');
		parent::__construct($pk);
	}
	
	public function setRequestedVersionID($cvID) {
		$this->cvID = $cvID;
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$d->setText(t("\"%s\" has pending changes and needs to be approved. View the page here: %s.", $c->getCollectionName(), $link));
		$d->setHTML(t("Page Submitted for Approval."));
		$d->setShortStatus(t("Pending Approval"));
		return $d;
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'info';
	}
	
	public function getWorkflowRequestApproveButtonClass() {
		return 'success';
	}
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Approve Page');
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$v = CollectionVersion::get($c, $this->cvID);
		$v->approve(false);
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}

	
}