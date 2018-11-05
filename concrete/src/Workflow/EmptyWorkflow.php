<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Workflow as ConcreteWorkflow;

/**
 * This is the final workflow that fires any time a workflow request is triggered. It HAS to be final because
 * it takes care of approving the workflow request object. If a previous workflow actually exists and fires,
 * this workflow is skipped because the previous workflow cancels it.
 * Class EmptyWorkflow
 * @package Concrete\Core\Workflow
 */
class EmptyWorkflow extends ConcreteWorkflow
{
    public function canApproveWorkflow()
    {
        return true;
    }

    public function start(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();
        $wpr = $req->approve($wp);
        $wp->delete();

        return $wpr;
    }

    public function getWorkflowProgressApprovalUsers(WorkflowProgress $wp)
    {
        return array();
    }

    public function getWorkflowProgressCurrentComment(WorkflowProgress $wp)
    {
        return false;
    }

    public function updateDetails($vars)
    {
    }

    public function loadDetails()
    {
    }

    public function canApproveWorkflowProgressObject(WorkflowProgress $wp)
    {
        return false;
    }

    public function getWorkflowProgressActions(WorkflowProgress $wp)
    {
        return array();
    }

    public function getWorkflowProgressStatusDescription(WorkflowProgress $wp)
    {
        return '';
    }
}
