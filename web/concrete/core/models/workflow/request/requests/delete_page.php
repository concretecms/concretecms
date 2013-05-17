<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_DeletePagePageWorkflowRequest extends PageWorkflowRequest {
	
	protected $wrStatusNum = 100;

	public function __construct() {
		$pk = PermissionKey::getByHandle('delete_page');
		parent::__construct($pk);
	}

	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$c = Page::getByID($this->cID, 'ACTIVE');
		$item = t('page');
		if ($c->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
			$item = t('stack');
		}
		$link = Loader::helper('navigation')->getLinkToCollection($c, true);
		$d->setEmailDescription(t("\"%s\" has been marked for deletion. View the page here: %s.", $c->getCollectionName(), $link));
		$d->setInContextDescription(t("This %s has been marked for deletion. ", $item));
		$d->setDescription(t("<a href=\"%s\">%s</a> has been marked for deletion. ", $link, $c->getCollectionName()));
		$d->setShortStatus(t("Pending Delete"));
		return $d;
	}
	
	public function getWorkflowRequestStyleClass() {
		return 'error';
	}
	
	public function getWorkflowRequestApproveButtonClass() {
		return 'error';
	}

	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="icon-white icon-trash"></i>';
	}	
	
	public function getWorkflowRequestApproveButtonText() {
		return t('Approve Delete');
	}

	public function approve(WorkflowProgress $wp) {
		$c = Page::getByID($this->getRequestedPageID());
		if ($c->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
			$c = Stack::getByID($this->getRequestedPageID());
			$c->delete();
			$wpr = new WorkflowProgressResponse();
			$wpr->setWorkflowProgressResponseURL(View::url('/dashboard/blocks/stacks', 'stack_deleted'));
			return $wpr;
		}

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