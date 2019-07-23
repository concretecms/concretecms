<?php
namespace Concrete\Core\Workflow\Request;

use HtmlObject\Element;
use Workflow;
use Loader;
use Page;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Permissions;
use PermissionKey;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use CollectionVersion;
use Events;
use Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class UnapprovePageRequest extends PageRequest
{
    protected $wrStatusNum = 30;

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
        $link = Loader::helper('navigation')->getLinkToCollection($c, true);
        $v = $c->getVersionObject();
        if (is_object($v)) {
            $d->setEmailDescription(t("Page unapproval requested for page: \"%s\".\n\nView the page here: %s.", $c->getCollectionName(), $link));
            $d->setDescription(t("Page %s submitted for unapproval.", '<a target="_blank" href="' . $c->getCollectionLink() . '">' . $c->getCollectionName() . '</a>'));
            $d->setInContextDescription(t("Page %s submitted for unapproval.", $c->getCollectionName()));
            $d->setShortStatus(t("Page Version Unapproval"));
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
        return '<i class="fa fa-thumbs-o-up"></i>';
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
        $span->addClass('fa fa-thumbs-down');
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
            Events::dispatch('on_page_version_submit_deny', $ev);

            $wpr = new WorkflowProgressResponse();
            $wpr->setWorkflowProgressResponseURL(\URL::to($c));

            return $wpr;
        }
    }


}
