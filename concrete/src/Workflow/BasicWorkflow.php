<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\HistoryEntry\BasicHistoryEntry as BasicWorkflowHistoryEntry;
use Concrete\Core\Workflow\Progress\Action\ApprovalAction as WorkflowProgressApprovalAction;
use Concrete\Core\Workflow\Progress\Action\CancelAction as WorkflowProgressCancelAction;
use Concrete\Core\Workflow\Progress\BasicData as BasicWorkflowProgressData;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Core;
use PermissionAccess;
use PermissionKey;
use User;
use UserInfo;

class BasicWorkflow extends \Concrete\Core\Workflow\Workflow
{
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\BasicWorkflowAssignment';
    }

    public function updateDetails($post)
    {
        $permissions = PermissionKey::getList('basic_workflow');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($this);
            $pt = $pk->getPermissionAssignmentObject();
            $paID = $post['pkID'][$pk->getPermissionKeyID()];
            $pt->clearPermissionAssignment();
            if ($paID > 0) {
                $pa = PermissionAccess::getByID($paID, $pk);
                if (is_object($pa)) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }
    }

    /**
     * Returns true if the logged-in user can approve the current workflow
     */
    public function canApproveWorkflow()
    {
        $pk = Key::getByHandle('approve_basic_workflow_action');
        $pk->setPermissionObject($this);
        return $pk->validate();
    }

    public function loadDetails()
    {
    }

    public function delete()
    {
        $db = Core::make('database')->connection();
        $db->executeQuery('DELETE FROM BasicWorkflowPermissionAssignments WHERE wfID = ?', array($this->wfID));
        parent::delete();
    }

    public function start(WorkflowProgress $wp)
    {
        // lets save the basic data associated with this workflow.
        $req = $wp->getWorkflowRequestObject();

        // Check if the workflow is not already approved
        if (is_object($req)) {

            if ($this->canApproveWorkflow()) {
                // Then that means we have the ability to approve the workflow we just started.
                // In that case, we transparently approve it, and skip the entry notification step.
                $wpr = $req->approve($wp);
                $wp->delete();

            } else {

                $db = Core::make('database')->connection();
                $db->executeQuery(
                    'INSERT INTO BasicWorkflowProgressData (wpID, uIDStarted) VALUES (?, ?)',
                    array($wp->getWorkflowProgressID(), $req->getRequesterUserID()));

                $ui = UserInfo::getByID($req->getRequesterUserID());

                // let's get all the people who are set to be notified on entry
                $message = t(
                    'On %s, user %s submitted the following request: %s',
                    Core::make('helper/date')->formatDateTime($wp->getWorkflowProgressDateAdded(), true),
                    $ui->getUserName(),
                    $req->getWorkflowRequestDescriptionObject()->getEmailDescription());
                $this->notify($wp, $message, 'notify_on_basic_workflow_entry');
            }

        }

    }

    protected function notify(
        WorkflowProgress $wp,
        $message,
        $permission = 'notify_on_basic_workflow_entry',
        $parameters = array()
    )
    {
        $nk = PermissionKey::getByHandle($permission);
        $nk->setPermissionObject($this);
        $users = $nk->getCurrentlyActiveUsers($wp);

        foreach ($users as $ui) {
            $mh = Core::make('helper/mail');
            $mh->addParameter('uName', $ui->getUserName());
            $mh->to($ui->getUserEmail());
            $adminUser = UserInfo::getByID(USER_SUPER_ID);
            $mh->from($adminUser->getUserEmail(), t('Basic Workflow'));
            $mh->addParameter('message', $message);
            foreach ($parameters as $key => $value) {
                $mh->addParameter($key, $value);
            }
            $mh->addParameter('siteName', Core::make('config')->get('concrete.site'));
            $mh->load('basic_workflow_notification');
            $mh->sendMail();
            unset($mh);
        }
    }

    public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp)
    {
        $bdw = new BasicWorkflowProgressData($wp);
        $ux = UserInfo::getByID($bdw->getUserStartedID());
        $req = $wp->getWorkflowRequestObject();
        $description = $req->getWorkflowRequestDescriptionObject()->getInContextDescription();

        return t(
            '%s Submitted by <strong>%s</strong> on %s.',
            $description,
            $ux->getUserName(),
            Core::make('helper/date')->formatDateTime($wp->getWorkflowProgressDateAdded(), true)
        );
    }

    public function getWorkflowProgressStatusDescription(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();

        return $req->getWorkflowRequestDescriptionObject()->getShortStatus();
    }

    public function cancel(WorkflowProgress $wp)
    {
        if ($this->canApproveWorkflowProgressObject($wp)) {

            $req = $wp->getWorkflowRequestObject();
            $bdw = new BasicWorkflowProgressData($wp);
            $u = new User();
            $bdw->markCompleted($u);

            $ux = UserInfo::getByID($bdw->getUserCompletedID());

            $message = t(
                "On %s, user %s cancelled the following request: \n\n---\n%s\n---\n\n",
                Core::make('helper/date')->formatDateTime($bdw->getDateCompleted(), true),
                $ux->getUserName(),
                $req->getWorkflowRequestDescriptionObject()->getEmailDescription()
            );
            $this->notify($wp, $message, 'notify_on_basic_workflow_deny');

            $hist = new BasicWorkflowHistoryEntry();
            $hist->setAction('cancel');
            $hist->setRequesterUserID($u->getUserID());
            $wp->addWorkflowProgressHistoryObject($hist);

            $wpr = $req->runTask('cancel', $wp);
            $wp->markCompleted();

            $bdw = new BasicWorkflowProgressData($wp);
            $bdw->delete();

            return $wpr;
        }
        return null;
    }

    public function canApproveWorkflowProgressObject(WorkflowProgress $wp)
    {
        return $this->canApproveWorkflow();
    }

    public function approve(WorkflowProgress $wp)
    {
        if ($this->canApproveWorkflowProgressObject($wp)) {
            $req = $wp->getWorkflowRequestObject();
            $bdw = new BasicWorkflowProgressData($wp);
            $u = new User();
            $bdw->markCompleted($u);

            $ux = UserInfo::getByID($bdw->getUserCompletedID());

            $message = t(
                "On %s, user %s approved the following request: \n\n---\n%s\n---\n\n",
                Core::make('helper/date')->formatDateTime($bdw->getDateCompleted(), true),
                $ux->getUserName(),
                $req->getWorkflowRequestDescriptionObject()->getEmailDescription()
            );
            $this->notify($wp, $message, 'notify_on_basic_workflow_approve');

            $wpr = $req->runTask('approve', $wp);
            $wp->markCompleted();

            $hist = new BasicWorkflowHistoryEntry();
            $hist->setAction('approve');
            $hist->setRequesterUserID($u->getUserID());
            $wp->addWorkflowProgressHistoryObject($hist);

            $bdw = new BasicWorkflowProgressData($wp);
            $bdw->delete();

            return $wpr;
        }
        return null;
    }

    public function getWorkflowProgressActions(WorkflowProgress $wp)
    {
        $pk = PermissionKey::getByHandle('approve_basic_workflow_action');
        $pk->setPermissionObject($this);
        $buttons = array();
        if ($this->canApproveWorkflowProgressObject($wp)) {
            $req = $wp->getWorkflowRequestObject();
            $button1 = new WorkflowProgressCancelAction();

            $button2 = new WorkflowProgressApprovalAction();
            $button2->setWorkflowProgressActionStyleClass($req->getWorkflowRequestApproveButtonClass());
            $button2->setWorkflowProgressActionStyleInnerButtonRightHTML(
                $req->getWorkflowRequestApproveButtonInnerButtonRightHTML()
            );
            $button2->setWorkflowProgressActionLabel($req->getWorkflowRequestApproveButtonText());

            $buttons[] = $button1;
            $buttons[] = $button2;
        }

        return $buttons;
    }

}
