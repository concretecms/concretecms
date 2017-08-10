<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\HistoryEntry\BasicHistoryEntry as BasicWorkflowHistoryEntry;
use Concrete\Core\Workflow\Progress\Action\ApprovalAction as WorkflowProgressApprovalAction;
use Concrete\Core\Workflow\Progress\Action\CancelAction as WorkflowProgressCancelAction;
use Concrete\Core\Workflow\Progress\BasicData as BasicWorkflowProgressData;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Core;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use PermissionKey;
use User;
use UserInfo;
use Concrete\Core\Localization\Localization;

class BasicWorkflow extends \Concrete\Core\Workflow\Workflow implements AssignableObjectInterface
{
    use AssignableObjectTrait;

    public function executeBeforePermissionAssignment($cascadeToChildren = true)
    {
        return;
    }

    public function setChildPermissionsToOverride()
    {
        return false;
    }

    public function setPermissionsToOverride()
    {
        return false;
    }

    public function getWorkflowProgressCurrentComment(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();
        if ($req) {
            return $req->getRequesterComment();
        }
    }

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

    public function getWorkflowProgressApprovalUsers(WorkflowProgress $wp)
    {
        $pk = Key::getByHandle('approve_basic_workflow_action');
        $pk->setPermissionObject($this);
        $access = $pk->getPermissionAssignmentObject()->getPermissionAccessObject();
        $users = [\UserInfo::getByID(USER_SUPER_ID)];
        $usersToRemove = [];

        if (is_object($access)) {
            // Loop through all items and get the relevant users.
            $items = $access->getAccessListItems(Key::ACCESS_TYPE_INCLUDE);
            foreach ($items as $item) {
                $entity = $item->getAccessEntityObject();
                $users = array_merge($entity->getAccessEntityUsers($access), $users);
            }

            // Now we loop through the array and remove
            $items = $access->getAccessListItems(Key::ACCESS_TYPE_EXCLUDE);
            foreach ($items as $item) {
                $entity = $item->getAccessEntityObject();
                foreach ($entity->getAccessEntityUsers($access) as $user) {
                    $usersToRemove[] = $user->getUserID();
                }
            }

            $users = array_unique($users);
            $usersToRemove = array_unique($usersToRemove);

            $users = array_filter($users, function ($element) use ($usersToRemove) {
                if (in_array($element->getUserID(), $usersToRemove)) {
                    return false;
                }

                return true;
            });
        }

        return $users;
    }

    /**
     * Returns true if the logged-in user can approve the current workflow.
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
        $db->executeQuery('DELETE FROM BasicWorkflowPermissionAssignments WHERE wfID = ?', [$this->wfID]);
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

                return $wpr;
            } else {
                $db = Core::make('database')->connection();
                $db->executeQuery(
                    'INSERT INTO BasicWorkflowProgressData (wpID, uIDStarted) VALUES (?, ?)',
                    [$wp->getWorkflowProgressID(), $req->getRequesterUserID()]);

                $ui = UserInfo::getByID($req->getRequesterUserID());

                // let's get all the people who are set to be notified on entry
                $message = [
                    "start",
                    $ui->getUserName(),
                    $req];
                $this->notify($wp, $message, 'notify_on_basic_workflow_entry');
            }
        }
    }

    protected function notify(
        WorkflowProgress $wp,
        $message,
        $permission = 'notify_on_basic_workflow_entry',
        $parameters = []
    ) {
        $nk = PermissionKey::getByHandle($permission);
        $nk->setPermissionObject($this);
        $users = $nk->getCurrentlyActiveUsers($wp);
        $loc = Localization::getInstance();
        $loc->pushActiveContext('email');
        $dt = $wp->getWorkflowProgressDateAdded();
        $dh = Core::make('helper/date');

        foreach ($users as $ui) {
            // Get user object of the receiver and set locale to their language
            $user = $ui->getUserObject();
            $lan = $user->getUserLanguageToDisplay();
            $loc->setLocale($lan);
            $mh = Core::make('helper/mail');
            $mh->addParameter('uName', $ui->getUserName());
            $mh->to($ui->getUserEmail());
            $adminUser = UserInfo::getByID(USER_SUPER_ID);
            $mh->from($adminUser->getUserEmail(), t('Basic Workflow'));
            $date = $dh->formatDateTime($dt, true); // Call here to translate datetime into users language
            $translatedMessage = $this->getTranslatedMessage($message, $date);
            $mh->addParameter('message', $translatedMessage);
            foreach ($parameters as $key => $value) {
                $mh->addParameter($key, $value);
            }
            $mh->addParameter('siteName', \Core::make('site')->getSite()->getSiteName());
            $mh->load('basic_workflow_notification');
            $mh->sendMail();
            unset($mh);
        }
        $loc->popActiveContext();
    }

    public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp)
    {
        $bdw = new BasicWorkflowProgressData($wp);
        $ux = UserInfo::getByID($bdw->getUserStartedID());
        if (is_object($ux)) {
            $userName = $ux->getUserName();
        } else {
            $userName = t('(Deleted User)');
        }
        $req = $wp->getWorkflowRequestObject();
        $description = $req->getWorkflowRequestDescriptionObject()->getInContextDescription();

        return t(
            '%s Submitted by <strong>%s</strong> on %s.',
            $description,
            $userName,
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

            $message = [
                "cancel",
                $ux->getUserName(),
                $req
            ];
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

            $message = [
                "approve",
                $ux->getUserName(),
                $req
            ];
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
        $buttons = [];
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

    private function getTranslatedMessage($message = null, $date)
    {

        if (is_array($message)) {
            switch ($message[0]) {
                case 'approve':
                    $message = t("On %s, user %s approved the following request: \n\n---\n%s\n---\n\n",
                        $date, // Date
                        $message[1], // UserName
                        $message[2]->getWorkflowRequestDescriptionObject()->getEmailDescription() // We get the Description Object here as it gets translated when called
                    );
                    break;
                case 'cancel':
                    $message = t(
                        "On %s, user %s cancelled the following request: \n\n---\n%s\n---\n\n",
                        $date,
                        $message[1],
                        $message[2]->getWorkflowRequestDescriptionObject()->getEmailDescription()
                    );
                    break;
                default:
                    $message = t(
                        "On %s, user %s submitted the following request: %s",
                        $date,
                        $message[1],
                        $message[2]->getWorkflowRequestDescriptionObject()->getEmailDescription()
                    );

            }
        }

        return $message;
    }
}
