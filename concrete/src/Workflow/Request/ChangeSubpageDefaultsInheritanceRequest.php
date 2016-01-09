<?php
namespace Concrete\Core\Workflow\Request;
use Workflow;
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

class ChangeSubpageDefaultsInheritanceRequest extends PageRequest {

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
		return 'btn-success';
	}

	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="fa fa-thumbs-o-up"></i>';
	}

	public function getWorkflowRequestApproveButtonText() {
		return t('Change Inheritance');
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$c->setOverrideTemplatePermissions($this->inheritance);
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(\URL::to($c));
		return $wpr;
	}


}
