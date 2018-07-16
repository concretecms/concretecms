<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\User\UserInfo;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Progress\UserProgress;
use PermissionKey;
use URL;

class DeleteUserRequest extends UserRequest
{

    protected $requestAction = 'delete';

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('delete_user');
        parent::__construct($pk);
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $ui = UserInfo::getByID($this->getRequestedUserID());
        $d->setEmailDescription(t("User account \"%s\" has been marked for deletion. The deletion request needs to be approved.",
            $ui->getUserName()));
        $d->setDescription(t("User %s Submitted for Deletion.", $ui->getUserName()));
        $d->setInContextDescription(t("User Submitted for Deletion."));
        $d->setShortStatus(t("Pending"));

        return $d;
    }

    public function approve(Progress $wp)
    {
        $ui = UserInfo::getByID($this->getRequestedUserID());
        $ui->delete();
        $wpr = parent::cancel($wp);

        // Make sure any workflow progress objects tied to this user are not
        // left lying around to mess up the waiting for me view.
        $workflowProgress = UserProgress::getList($this->getRequestedUserID(), [
            'wpIsCompleted' => 0,
            'wpApproved' => 0,
        ]);
        foreach ($workflowProgress as $wp) {
            // Skip the deletion of the current progress object as that would
            // cause problems in the code that follows.
            $wr = $wp->getWorkflowRequestObject();
            if ($wr->getWorkflowRequestID() !== $this->getWorkflowRequestID()) {
                $wp->delete();
            }
        }

        $url = (string) URL::to('/dashboard/users/search/view', $this->getRequestedUserID(), 'deleted');
        $wpr->setWorkflowProgressResponseURL($url);
        $wpr->message = t("User %s has been deleted.", $ui->getUserName());

        return $wpr;
    }

    /**
     * After canceling delete request, do nothing
     */
    public function cancel(Progress $wp)
    {
        $ui = UserInfo::getByID($this->getRequestedUserID());
        $wpr = parent::cancel($wp);
        $wpr->message = t("User deletion request has been cancelled.");

        return $wpr;
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
        return t('Delete User');
    }

    public function getWorkflowRequestAdditionalActions(Progress $wp)
    {
        $buttons = array();
        $button = new WorkflowProgressAction();
        $button->setWorkflowProgressActionLabel(t('Review'));
        $button->addWorkflowProgressActionButtonParameter('dialog-title', t('User Details'));
        $button->addWorkflowProgressActionButtonParameter('dialog-width', '420');
        $button->addWorkflowProgressActionButtonParameter('dialog-height', '310');
        $button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/user_details?uID=' . $this->getRequestedUserID());
        $button->setWorkflowProgressActionStyleClass('dialog-launch');
        $buttons[] = $button;

        return $buttons;
    }

    /**
     * Gets the translated text of action of user workflow request
     *
     * @return string
     */
    public function getRequestActionText()
    {
        return t("Deletion");
    }
}
