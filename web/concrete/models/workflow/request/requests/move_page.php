<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class MovePagePageWorkflowRequest extends PageWorkflowRequest {
	
	protected $targetCID;
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('move_or_copy_page');
		parent::__construct($pk);
	}
	
	public function setRequestedTargetPage($c) {
		$this->targetCID = $c->getCollectionID();
	}
	
	public function getRequestedTargetPageID() {
		return $this->targetCID;
	}	
	
	public function getWorkflowRequestDescription() {
		$uName = t('Unknown');
		$ui = $this->getWorkflowRequestUserObject();
		if (is_object($ui)) {
			$uName = $ui->getUserName();
		}
		$wp = $this->getCurrentWorkflowProgressObject();
		$target = Page::getByID($this->targetCID, 'ACTIVE');
		if (is_object($target) && !$target->isError()) {
			return t('User <strong>%s</strong> requested that this page be moved beneath <strong>%s</strong> on %s.', $uName, $target->getCollectionName(), date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateAdded())));
		}
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'info';
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
			$button2->setWorkflowRequestActionStyleClass('success');
			$button2->setWorkflowRequestActionLabel(t('Approve'));
			$button2->setWorkflowRequestActionTask('approve');

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
			$wpr = new WorkflowProgressResponse();
			$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
			return $wpr;
		}
	}
	
	public function action_approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$cParentID = $c->getCollectionParentID();
		$dc = Page::getByID($this->targetCID);
		if (is_object($c) && is_object($dc) && (!$c->isError()) && (!$dc->isError())) { 
			$cp = new Permissions($c);
			if ($cp->canApprovePageVersions()) {
				$dcp = new Permissions($dc);
				if ($c->canMoveCopyTo($dc)) {
					$ct = CollectionType::getByID($c->getCollectionTypeID());
					if ($dcp->canAddSubpage($ct)) {
						$wp->delete();			
						$c->move($dc);
						$wpr = new WorkflowProgressResponse();
						$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cParentID);
						return $wpr;
					}
				}
			}
		}
	}	
}