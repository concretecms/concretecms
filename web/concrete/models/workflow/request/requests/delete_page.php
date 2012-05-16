<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class DeletePagePageWorkflowRequest extends PageWorkflowRequest {
	
	const REQUEST_STATUS_NUM = 100;

	public function __construct() {
		$pk = PermissionKey::getByHandle('delete_page');
		parent::__construct($pk);
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$d->setText(t("\"%s\" has been marked for deletion. View the page here: %s.", $c->getCollectionName(), $link));
		$d->setHTML(t("This page has been marked for deletion. "));
		return $d;
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'error';
	}
	
	public function getWorkflowRequestApproveButtonClass() {
		return 'error';
	}
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Delete Page');
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$cParentID = $c->getCollectionParentID();
		if (ENABLE_TRASH_CAN) {
			$c->moveToTrash();
		} else {
			$c->delete();
		}
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cParentID);
		return $wpr;
	}

	
}