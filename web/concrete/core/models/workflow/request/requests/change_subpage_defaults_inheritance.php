<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_ChangeSubpageDefaultsInheritancePageWorkflowRequest extends PageWorkflowRequest {
	
	protected $wrStatusNum = 30;

	public function __construct() {
		$pk = PermissionKey::getByHandle('edit_page_permissions');
		parent::__construct($pk);
	}
	
	public function setPagePermissionsInheritance($inheritance) {
		$this->inheritance = $inheritance;
	}

	public function getPagePermissionsInheritance() {
		return $this->inheritance;
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$d->setEmailDescription(t("\"%s\" has pending sub-page permission inhiterance changes. View the page here: %s.", $c->getCollectionName(), $link));
		if ($this->inheritance == 0) {
			$d->setInContextDescription(t("Sub-pages pending change to inherit permissions from page type."));
		} else {
			$d->setInContextDescription(t("Sub-pages pending change to inherit permissions from parent."));
		}
		$d->setDescription(t("<a href=\"%s\">%s</a> has pending sub-page permission inhiterance changes.", $link, $c->getCollectionName()));
		$d->setShortStatus(t("Sub-Page Inheritance Changes"));
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
		return t('Change Inheritance');
	}
	
	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$c->setOverrideTemplatePermissions($this->inheritance);
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}

	
}