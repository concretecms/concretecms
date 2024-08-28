<?php

namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Page\Collection\Version\Version as CollectionVersion;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use HtmlObject\Element;

class UnapprovePageRequest extends PageRequest
{
    protected $wrStatusNum = 30;

    protected $cvID;

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('approve_page_versions');
        parent::__construct($pk);
    }

    public function setRequestedVersionID($cvID)
    {
        $this->cvID = $cvID;
    }

    public function getRequestedVersionID()
    {
        return $this->cvID;
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $c = Page::getByID($this->cID, $this->cvID);
        if ($c && !$c->isError()) {
            $link = $c->getCollectionLink();
            $v = $c->getVersionObject();
            if (is_object($v) && !$v->isError()) {
                $d->setEmailDescription(t("Page unapproval requested for page: \"%s\".\n\nView the page here: %s.", $c->getCollectionName(), $link));
                $d->setDescription(t('Page %s submitted for unapproval.', '<a target="_blank" href="' . $c->getCollectionLink() . '">' . $c->getCollectionName() . '</a>'));
                $d->setInContextDescription(t('Page %s submitted for unapproval.', $c->getCollectionName()));
                $d->setShortStatus(t('Page Version Unapproval'));
            } else {
                $d->setEmailDescription(t('Deleted Page Version.'));
                $d->setInContextDescription(t('Deleted Page Version.'));
                $d->setDescription(t('Deleted Page Version.'));
                $d->setShortStatus(t('Deleted Page Version.'));
            }
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
        return t('Unapprove');
    }

    public function trigger()
    {
        $page = Page::getByID($this->cID, $this->cvID);

        return parent::trigger();
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fas fa-thumbs-down');

        return $span;
    }

    public function approve(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        $v = CollectionVersion::get($c, $this->cvID);
        if ($v) {
            $v->deny();

            $ev = new \Concrete\Core\Page\Collection\Version\Event($c);
            $ev->setCollectionVersionObject($v);

            $app = Application::getFacadeApplication();
            $app['director']->dispatch('on_page_version_submit_deny', $ev);

            $wpr = new WorkflowProgressResponse();
            $wpr->setWorkflowProgressResponseURL(\URL::to($c));

            return $wpr;
        }
    }
}
