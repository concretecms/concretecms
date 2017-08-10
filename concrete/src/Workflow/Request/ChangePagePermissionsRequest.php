<?php
namespace Concrete\Core\Workflow\Request;

use Workflow;
use Loader;
use Page;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Permissions;
use PermissionKey;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Permission\Set as PermissionSet;

class ChangePagePermissionsRequest extends PageRequest
{
    protected $wrStatusNum = 30;

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('edit_page_permissions');
        parent::__construct($pk);
    }

    public function setPagePermissionSet(PermissionSet $set)
    {
        $this->permissionSet = $set;
    }

    public function getPagePermissionSet()
    {
        return $this->permissionSet;
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $c = Page::getByID($this->cID, 'ACTIVE');
        $link = Loader::helper('navigation')->getLinkToCollection($c, true);
        $d->setEmailDescription(t("\"%s\" has pending permission changes. View the page here: %s.", $c->getCollectionName(), $link));
        $d->setInContextDescription(t("Page Submitted for Permission Changes."));
        $d->setDescription(t("<a href=\"%s\">%s</a> submitted for Permission Changes.", $link, $c->getCollectionName()));
        $d->setShortStatus(t("Permission Changes"));

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
        return t('Change Permissions');
    }

    public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp)
    {
        $buttons = array();
        $w = $wp->getWorkflowObject();
        if ($w->canApproveWorkflowProgressObject($wp)) {
            $c = Page::getByID($this->cID, 'ACTIVE');
            $button = new WorkflowProgressAction();
            $button->setWorkflowProgressActionLabel(t('View Pending Permissions'));
            $button->addWorkflowProgressActionButtonParameter('dialog-title', t('Pending Permissions'));
            $button->addWorkflowProgressActionButtonParameter('dialog-width', '400');
            $button->addWorkflowProgressActionButtonParameter('dialog-height', '360');
            $button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/change_page_permissions?wpID=' . $wp->getWorkflowProgressID());
            $button->setWorkflowProgressActionStyleClass('dialog-launch');
            $buttons[] = $button;
        }

        return $buttons;
    }

    public function approve(WorkflowProgress $wp)
    {
        $c = Page::getByID($this->getRequestedPageID());
        $ps = $this->getPagePermissionSet();
        $assignments = $ps->getPermissionAssignments();
        foreach ($assignments as $pkID => $paID) {
            $pk = PermissionKey::getByID($pkID);
            $pk->setPermissionObject($c);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            if ($paID > 0) {
                $pa = PermissionAccess::getByID($paID, $pk);
                if (is_object($pa)) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }
        $c->refreshCache();
        $wpr = new WorkflowProgressResponse();
        $wpr->setWorkflowProgressResponseURL(\URL::to($c));

        return $wpr;
    }
}
