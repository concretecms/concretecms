<?php

namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Page\Collection\Version\Version as CollectionVersion;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class ApproveStackRequest extends ApprovePageRequest
{
    public function approve(WorkflowProgress $wp)
    {
        $s = Stack::getByID($this->getRequestedPageID());
        $v = CollectionVersion::get($s, $this->cvID);
        $v->approve(false);
        if ($s->getStackName() != $v->getVersionName()) {
            // The stack name has changed so we need to
            // update that for the stack object as well.
            $s->update(['stackName' => $v->getVersionName()]);
        }

        $ev = new \Concrete\Core\Page\Collection\Version\Event($s);
        $ev->setCollectionVersionObject($v);

        $app = Application::getFacadeApplication();
        $app['director']->dispatch('on_page_version_submit_approve', $ev);

        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL(\URL::to($s));

        return $wpr;
    }
}
