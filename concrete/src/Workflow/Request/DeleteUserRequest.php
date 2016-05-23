<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\User\UserInfo;
use Concrete\Core\Workflow\Progress\Progress;
use PermissionKey;
use URL;
use \Concrete\Core\Workflow\Description as WorkflowDescription;
use Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;

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
        return 'btn-success';
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
        $button->setWorkflowProgressActionStyleInnerButtonLeftHTML('<i class="fa fa-eye"></i>');
        $button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/user_details?uID=' . $this->getRequestedUserID());
        $button->setWorkflowProgressActionStyleClass('btn-default dialog-launch');
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