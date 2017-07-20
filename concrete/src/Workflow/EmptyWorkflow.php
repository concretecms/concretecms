<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Workflow as ConcreteWorkflow;

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

    public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp)
    {
        return '';
    }

    public function getWorkflowProgressStatusDescription(WorkflowProgress $wp)
    {
        return '';
    }
}
