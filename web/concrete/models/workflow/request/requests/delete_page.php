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
	
	public function __construct() {
		$this->permissionKey = PermissionKey::getByHandle('delete_page');
		parent::__construct();
	}
	
	public function getWorkflowRequestExternalDescription() {
		$uName = t('Unknown');
		$ui = $this->getWorkflowRequestUserObject();
		if (is_object($ui)) {
			$uName = $ui->getUserName();
		}
		$wp = $this->getCurrentWorkflowProgressObject();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		return t("User %s marked \"%s\" for deletion on %s.\n\nView the page here: %s", $uName, $c->getCollectionName(), date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded())), $link);
	}
	
	public function getWorkflowRequestDescription() {
		$uName = t('Unknown');
		$ui = $this->getWorkflowRequestUserObject();
		if (is_object($ui)) {
			$uName = $ui->getUserName();
		}
		$wp = $this->getCurrentWorkflowProgressObject();
		return t('User <strong>%s</strong> marked this page for deletion on %s.', $uName, date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded())));
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'error';
	}
	
	public function getWorkflowRequestActions() {
		$buttons = array();
		$c = Page::getByID($this->getRequestedPageID());
		$cp = new Permissions($c);
		if ($cp->canApprovePageVersions()) {
			$button1 = new WorkflowRequestAction();
			$button1->setWorkflowRequestActionLabel(t('Cancel'));
			$button1->setWorkflowRequestActionTask('cancel');

			$button2 = new WorkflowRequestAction();
			$button2->setWorkflowRequestActionStyleClass('error');
			$button2->setWorkflowRequestActionLabel(t('Delete'));
			$button2->setWorkflowRequestActionTask('delete');

			$buttons[] = $button1;
			$buttons[] = $button2;
		}
		return $buttons;
	}

	/** 
	 * @private
	 */
	public function action_cancel(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$cp = new Permissions($c);
		if ($cp->canApprovePageVersions()) {
			$wp->delete();			
		}
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}
	
	public function action_delete(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$cParentID = $c->getCollectionParentID();
		$cp = new Permissions($c);
		if ($cp->canApprovePageVersions()) {
			$wp->delete();
			if (ENABLE_TRASH_CAN) {
				$c->moveToTrash();
			} else {
				$c->delete();
			}
		}
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cParentID);
		return $wpr;
	}

	
}