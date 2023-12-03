<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Page\Page;
use Config;
use Loader;
use Stack;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use PermissionKey;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use URL;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class DeletePageRequest extends PageRequest
{
    protected $wrStatusNum = 100;

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('delete_page');
        parent::__construct($pk);
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $c = Page::getByID($this->cID, 'ACTIVE');
        if ($c && !$c->isError()) {
            $item = t('page');
            if ($c->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $item = t('stack');
            }
            $link = $c->getCollectionLink();
            $d->setEmailDescription(t("\"%s\" has been marked for deletion. View the page here: %s.", $c->getCollectionName(), $link));
            $d->setInContextDescription(t("This %s has been marked for deletion. ", $item));
            $d->setDescription(t("<a href=\"%s\">%s</a> has been marked for deletion. ", $link, $c->getCollectionName()));
            $d->setShortStatus(t("Pending Delete"));
        } else {
            $d->setEmailDescription(t('Deleted page.'));
            $d->setInContextDescription(t('Deleted page.'));
            $d->setDescription(t('Deleted page.'));
            $d->setShortStatus(t('Deleted page.'));
        }

        return $d;
    }

    public function getWorkflowRequestStyleClass()
    {
        return 'danger';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fas fa-trash-alt"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        return t('Approve Delete');
    }

    public function approve(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        if ($c->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            $c = Stack::getByID($this->getRequestedPageID());
            $c->delete();
            $wpr = new WorkflowProgressResponse();
            $wpr->setWorkflowProgressResponseURL(URL::to(STACKS_LISTING_PAGE_PATH, 'stack_deleted'));

            return $wpr;
        }

        $cParentID = $c->getCollectionParentID();
        if (Config::get('concrete.misc.enable_trash_can')) {
            $c->moveToTrash();
        } else {
            $c->delete();
        }
        $wpr = new WorkflowProgressResponse();
        $parent = Page::getByID($cParentID, 'ACTIVE');
        $wpr->setWorkflowProgressResponseURL(\URL::to($parent));

        return $wpr;
    }
}
