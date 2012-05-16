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
	const REQUEST_STATUS_NUM = 50;
	
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
	
	public function setSaveOldPagePath($r) {
		$this->saveOldPagePath = $r;
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$target = Page::getByID($this->targetCID, 'ACTIVE');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$targetLink = Loader::helper('navigation')->getLinkToCollection($target, true);
		$d->setText(t("\"%s\" is pending a move to beneath \"%s\". Source Page: %s. Target Page: %s", $c->getCollectionName(), $target->getCollectionName(), $link, $targetLink));
		$d->setHTML(t("This page is pending a move beneath <strong><a href=\"%s\">%s</a></strong>. ", $targetLink, $target->getCollectionName()));
		return $d;
	}

	public function getWorkflowRequestStyleClass() {
		return 'info';
	}

	public function getWorkflowRequestApproveButtonClass() {
		return 'info';
	}
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Approve Move');
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$dc = Page::getByID($this->targetCID);
		if (is_object($c) && is_object($dc) && (!$c->isError()) && (!$dc->isError())) { 
			if ($c->canMoveCopyTo($dc)) {
				if ($this->saveOldPagePath) {
					$nc2 = $c->move($dc, true);
				} else {
					$nc2 = $c->move($dc);
				}
				$wpr = new WorkflowProgressResponse();
				$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
				return $wpr;
			}
		}
	}
}