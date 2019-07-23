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

class ApprovePageRequest extends PageRequest
{
    protected $wrStatusNum = 30;
    private $isScheduled = false;
    private $cvPublishDate;
    private $cvPublishEndDate;

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

    protected function isNewPageRequest()
    {
        $active = Page::getByID($this->cID, 'ACTIVE');
        if (is_object($active) && !$active->isError()) {
            $version = $active->getVersionObject();
            if (is_object($version) && $version->getVersionID()) {
                return false;
            }
        }
        return true;
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $c = Page::getByID($this->cID, 'RECENT');
        $link = Loader::helper('navigation')->getLinkToCollection($c, true);
        $v = $c->getVersionObject();
        if (is_object($v)) {
            $comments = $c->getVersionObject()->getVersionComments();

            if (!$this->isNewPageRequest()) {
                // new version of existing page
                $d->setEmailDescription(t("\"%s\" has pending changes and needs to be approved.\n\nVersion Comments: %s\n\nView the page here: %s.",
                    $c->getCollectionName(), $comments, $link));
                $d->setDescription(t("Version %s of Page <a target=\"_blank\" href=\"%s\">%s</a> submitted for Approval.", $this->cvID, $link,
                    $c->getCollectionName()));
                $d->setInContextDescription(t("Page Version %s Submitted for Approval.", $this->cvID));
                $d->setShortStatus(t("Pending Approval"));
            } else {
                // Completely new page.
                $d->setEmailDescription(t("New page created: \"%s\". This page requires approval.\n\nAuthor Comments: %s\n\nView the page here: %s.",
                    $c->getCollectionName(), $comments, $link));
                $d->setDescription(t("New Page: <a target=\"_blank\" href=\"%s\">%s</a>", $link, $c->getCollectionName()));
                $d->setInContextDescription(t("New Page %s submitted for approval.", $this->cvID));
                $d->setShortStatus(t("New Page"));
            }
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
        return t('Approve');
    }

    public function trigger()
    {
        $page = Page::getByID($this->cID, $this->cvID);

        return parent::trigger();
    }

    public function getRequestIconElement()
    {
        if (!$this->isNewPageRequest()) {
            return parent::getRequestIconElement();
        }

        $span = new Element('i');
        $span->addClass('fa fa-file');
        return $span;
    }


    public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp)
    {
        $buttons = array();
        $c = Page::getByID($this->cID, 'ACTIVE');
        $cp = new Permissions($c);
        if ($cp->canViewPageVersions()) {
            $button = new WorkflowProgressAction();
            $button->setWorkflowProgressActionLabel(t('Review'));
            $button->addWorkflowProgressActionButtonParameter('dialog-title', t('Compare Versions'));
            $button->addWorkflowProgressActionButtonParameter('dialog-width', '90%');
            $button->addWorkflowProgressActionButtonParameter('dialog-height', '70%');
            $button->addWorkflowProgressActionButtonParameter('data-dismiss-alert', 'page-alert');
            $button->addWorkflowProgressActionButtonParameter('dialog-height', '70%');
            $button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/approve_page_preview?wpID=' . $wp->getWorkflowProgressID());
            $button->setWorkflowProgressActionStyleClass('dialog-launch');
            $buttons[] = $button;
        }

        return $buttons;
    }

    public function cancel(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID(), $this->cvID);

        $ev = new \Concrete\Core\Page\Collection\Version\Event($c);
        $v = $c->getVersionObject();
        $ev->setCollectionVersionObject($v);
        Events::dispatch('on_page_version_deny', $ev);

        parent::cancel($wp);
    }

    public function approve(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        $v = CollectionVersion::get($c, $this->cvID);
        $v->approve(false, $this->cvPublishDate, $this->cvPublishEndDate);

        $ev = new \Concrete\Core\Page\Collection\Version\Event($c);
        $ev->setCollectionVersionObject($v);
        Events::dispatch('on_page_version_submit_approve', $ev);

        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL(\URL::to($c));

        return $wpr;
    }

    public function scheduleVersion($cvPublishDate, $cvPublishEndDate)
    {
        $this->isScheduled = true;
        $this->cvPublishDate = $cvPublishDate;
        $this->cvPublishEndDate = $cvPublishEndDate;
    }

    public function getRequesterComment()
    {
        $c = Page::getByID($this->getRequestedPageID());
        $v = CollectionVersion::get($c, $this->cvID);
        return $v->getVersionComments();
    }

}
