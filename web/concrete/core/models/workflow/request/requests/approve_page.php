<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_ApprovePagePageWorkflowRequest extends PageWorkflowRequest {
	
	protected $wrStatusNum = 30;

	public function __construct() {
		$pk = PermissionKey::getByHandle('approve_page_versions');
		parent::__construct($pk);
	}
	
	public function setRequestedVersionID($cvID) {
		$this->cvID = $cvID;
	}

	public function getRequestedVersionID() {
		return $this->cvID;
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'RECENT');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$d->setEmailDescription(t("\"%s\" has pending changes and needs to be approved. View the page here: %s.", $c->getCollectionName(), $link));
		$d->setDescription(t("Version %s of Page <a href=\"%s\">%s</a> submitted for Approval.", $this->cvID, $link, $c->getCollectionName()));
		$d->setInContextDescription(t("Page Version %s Submitted for Approval.", $this->cvID));
		$d->setShortStatus(t("Pending Approval"));
		return $d;
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'info';
	}
	
	public function getWorkflowRequestApproveButtonClass() {
		return 'success';
	}
	
	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="icon-white icon-thumbs-up"></i>';
	}		
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Approve Page');
	}
	
	public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp) {
		
		$buttons = array();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$cp = new Permissions($c);
		if ($cp->canViewPageVersions()) {
			$button = new WorkflowProgressAction();
			$button->setWorkflowProgressActionLabel(t('Compare Versions'));
			$button->addWorkflowProgressActionButtonParameter('dialog-title', t('Compare Versions'));
			$button->addWorkflowProgressActionButtonParameter('dialog-width', '90%');
			$button->addWorkflowProgressActionButtonParameter('dialog-height', '70%');
			$button->setWorkflowProgressActionStyleInnerButtonLeftHTML('<i class="icon-eye-open"></i>');
			$button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/approve_page_preview.php?wpID=' . $wp->getWorkflowProgressID());
			$button->setWorkflowProgressActionStyleClass('dialog-launch');
			$buttons[] = $button;
		}
		return $buttons;
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$v = CollectionVersion::get($c, $this->cvID);
		$v->approve(false);
		Events::fire('on_page_version_submit_approve', $c);
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}

	
}