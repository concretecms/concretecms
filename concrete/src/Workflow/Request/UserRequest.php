<?php
namespace Concrete\Core\Workflow\Request;

use HtmlObject\Element;
use URL;
use Concrete\Core\User\UserInfo;
use Core;
use Loader;
use PermissionKey;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;

abstract class UserRequest extends Request
{

    public function setRequestedUserID($requestedUID)
    {
        $this->requestedUID = $requestedUID;
    }

    public function getRequestedUserID()
    {
        return $this->requestedUID;
    }

    /**
     * Gets the action of user workflow request. There are four actions:
     * activate, register_activate, deactivate and delete
     *
     * @return string
     */
    public function getRequestAction()
    {
        return $this->requestAction;
    }

    public function trigger()
    {
        $user = UserInfo::getByID($this->requestedUID);
        $pk = PermissionKey::getByID($this->pkID);
        $pk->setPermissionObject($user);

        return parent::triggerRequest($pk);
    }

    public function approve(WorkflowProgress $wp)
    {
        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL((string) URL::to('/dashboard/users/search/view/', $this->getRequestedUserID()));

        return $wpr;
    }

    public function cancel(WorkflowProgress $wp)
    {
        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL((string) URL::to('/dashboard/users/search/view/', $this->getRequestedUserID(), '/workflow_canceled'));

        return $wpr;
    }

    public function addWorkflowProgress(Workflow $wf)
    {
        Loader::model('workflow/progress/categories/user');
        $uwp = UserWorkflowProgress::add($wf, $this);
        $r = $uwp->start();
        $uwp->setWorkflowProgressResponseObject($r);

        return $uwp;
    }

    /**
     * Override the runTask method in order to launch the cancel function
     * correctly (to trigger user deletion for instance)
     */
    public function runTask($task, WorkflowProgress $wp)
    {
        $wpr = parent::runTask($task, $wp);
        if (!is_object($wpr) && method_exists($this, $task)) {
            if ($task == 'cancel') {
                // we check to see if any other outstanding workflowprogress requests have this id
                // if they don't we proceed
                $db = Loader::db();
                $num = $db->GetOne('select count(wpID) as total from WorkflowProgress where wpID <> ? and wrID = ? and wpIsCompleted = 0',
                    array(
                        $wp->getWorkflowProgressID(),
                        $this->getWorkflowRequestID()
                    ));
                if ($num == 0) {
                    $wpr = call_user_func_array(array($this, $task), array($wp));

                    return $wpr;
                }
            }
        }

        return $wpr;
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fa fa-user');
        return $span;
    }
}