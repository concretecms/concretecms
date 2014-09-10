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

class ApprovePageRequest extends PageRequest {
	
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
		return 'btn-success';
	}
	
	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="fa fa-thumbs-o-up"></i>';
	}		
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Approve Page');
	}
	
	public function trigger() {
		$page = Page::getByID($this->cID, $this->cvID);
		return parent::trigger();
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
            $button->addWorkflowProgressActionButtonParameter('data-dismiss-alert', 'page-alert');
            $button->addWorkflowProgressActionButtonParameter('dialog-height', '70%');
			$button->setWorkflowProgressActionStyleInnerButtonLeftHTML('<i class="fa fa-eye"></i>');
			$button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/approve_page_preview?wpID=' . $wp->getWorkflowProgressID());
			$button->setWorkflowProgressActionStyleClass('btn-default dialog-launch');
			$buttons[] = $button;
		}
		return $buttons;
	}

	public function cancel(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID(), $this->cvID);

		$ev = new \Concrete\Core\Page\Collection\Version\Event($c);
		$v = $c->getVersionObject();
		$ev->setCollectionVersionObject($v);
		Events::dispatch('on_page_version_deny', $ev);

		parent::cancel($wp);
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		$v = CollectionVersion::get($c, $this->cvID);
		$v->approve(false);

		$ev = new \Concrete\Core\Page\Collection\Version\Event($c);
		$ev->setCollectionVersionObject($v);
		Events::dispatch('on_page_version_submit_approve', $ev);

		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		return $wpr;
	}

	
}