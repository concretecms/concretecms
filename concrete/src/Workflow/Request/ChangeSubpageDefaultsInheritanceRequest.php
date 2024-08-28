<?php

namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class ChangeSubpageDefaultsInheritanceRequest extends PageRequest
{
    protected $wrStatusNum = 30;

    protected $inheritance;

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('edit_page_permissions');
        parent::__construct($pk);
    }

    public function setPagePermissionsInheritance($inheritance)
    {
        $this->inheritance = $inheritance;
    }

    public function getPagePermissionsInheritance()
    {
        return $this->inheritance;
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $c = Page::getByID($this->cID, 'ACTIVE');
        if ($c && !$c->isError()) {
            $link = $c->getCollectionLink();
            $d->setEmailDescription(t('"%s" has pending sub-page permission inhiterance changes. View the page here: %s.', $c->getCollectionName(), $link));
            if ($this->inheritance == 0) {
                $d->setInContextDescription(t('Sub-pages pending change to inherit permissions from page type.'));
            } else {
                $d->setInContextDescription(t('Sub-pages pending change to inherit permissions from parent.'));
            }
            $d->setDescription(t('<a href="%s">%s</a> has pending sub-page permission inhiterance changes.', $link, $c->getCollectionName()));
            $d->setShortStatus(t('Sub-Page Inheritance Changes'));
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
        return 'info';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fas fa-thumbs-up"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        return t('Change Inheritance');
    }

    public function approve(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        $c->setOverrideTemplatePermissions($this->inheritance);
        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL(\URL::to($c));

        return $wpr;
    }
}
