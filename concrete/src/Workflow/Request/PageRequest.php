<?php

namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Workflow;
use HtmlObject\Element;

abstract class PageRequest extends Request
{
    protected $cID;

    public function setRequestedPage($c)
    {
        $cID = ($c->getCollectionPointerOriginalID() > 0) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();
        $this->cID = $cID;
    }

    public function getRequestedPageID()
    {
        return $this->cID;
    }

    public function getRequestedPageVersionID()
    {
        if (isset($this->cvID)) {
            return $this->cvID;
        }
        $c = Page::getByID($this->cID, 'RECENT');

        return $c->getVersionID();
    }

    public function setRequestedPageVersionID($cvID)
    {
        $this->cvID = $cvID;
    }

    public function addWorkflowProgress(Workflow $wf)
    {
        $pwp = PageWorkflowProgress::add($wf, $this);
        $r = $pwp->start();
        $pwp->setWorkflowProgressResponseObject($r);

        return $pwp;
    }

    public function trigger()
    {
        $page = Page::getByID($this->cID);
        $pk = PermissionKey::getByID($this->pkID);
        $pk->setPermissionObject($page);

        return parent::triggerRequest($pk);
    }

    public function cancel(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL(\URL::to($c));

        return $wpr;
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fas fa-file-alt');

        return $span;
    }
}
