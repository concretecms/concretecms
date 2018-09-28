<?php
namespace Concrete\Core\Workflow\Request;

use Config;
use Loader;
use Page;
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
        $item = t('page');
        if ($c->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            $item = t('stack');
        }
        $link = Loader::helper('navigation')->getLinkToCollection($c, true);
        $d->setEmailDescription(t("\"%s\" has been marked for deletion. View the page here: %s.", $c->getCollectionName(), $link));
        $d->setInContextDescription(t("This %s has been marked for deletion. ", $item));
        $d->setDescription(t("<a href=\"%s\">%s</a> has been marked for deletion. ", $link, $c->getCollectionName()));
        $d->setShortStatus(t("Pending Delete"));

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
        return '<i class="fa fa-trash-o"></i>';
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
            $wpr->setWorkflowProgressResponseURL(URL::to('/dashboard/blocks/stacks', 'stack_deleted'));

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
