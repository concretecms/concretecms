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
class MovePageRequest extends PageRequest {

	protected $targetCID;
	protected $wrStatusNum = 50;

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
		$d->setEmailDescription(t("\"%s\" is pending a move to beneath \"%s\". Source Page: %s. Target Page: %s.", $c->getCollectionName(), $target->getCollectionName(), $link, $targetLink));
		$d->setInContextDescription(t("This page is pending a move beneath <strong><a href=\"%s\">%s</a></strong>. ", $targetLink, $target->getCollectionName()));
		$d->setDescription(t("<a href=\"%s\">%s</a> is pending a move beneath <strong><a href=\"%s\">%s</a></strong>. ", $link, $c->getCollectionName(), $targetLink, $target->getCollectionName()));
		$d->setShortStatus(t("Pending Move"));
		return $d;
	}

	public function getWorkflowRequestStyleClass() {
		return 'info';
	}

	public function getWorkflowRequestApproveButtonClass() {
		return 'info';
	}

	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="fa fa-share-alt"></i>';
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
				    // retain old page path.
                    $path = $c->getCollectionPathObject();
                    if (is_object($path)) {
                        $c->addAdditionalPagePath($path->getPagePath());
                    }
                }
                $nc2 = $c->move($dc);
                $wpr = new WorkflowProgressResponse();
				$wpr->setWorkflowProgressResponseURL(\URL::to($c));
				return $wpr;
			}
		}
	}
}
