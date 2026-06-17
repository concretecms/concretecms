<?php
namespace Concrete\Core\Application\Service;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;
use Concrete\Core\Workflow\Progress\PageProgress;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use View;

class Composer
{
    /**
     * @param Type $pagetype
     * @param bool|\Page $page
     */
    public function display(Type $pagetype, $page = false)
    {
        $pagetype->renderComposerOutputForm($page);
    }

    /**
     * @param Type $pagetype
     * @param bool|\Page $page
     */
    public function displayButtons(Type $pagetype, $page = false)
    {
        View::element('page_types/composer/form/output/buttons', array(
            'pagetype' => $pagetype,
            'page' => $page,
        ));
    }

    /**
     * @param Type $pt
     * @param \Controller $cnt
     */
    public function addAssetsToRequest(Type $pt, \Controller $cnt)
    {
        $list = PageTypeComposerControl::getList($pt);
        foreach ($list as $l) {
            $l->addAssetsToRequest($cnt);
        }
    }

    public function canSubmitWorkflow(Page $c): bool
    {
        $pk = Key::getByHandle('approve_page_versions');
        $pk->setPermissionObject($c);
        $pa = $pk->getPermissionAccessObject();
        $workflows = [];
        $canApproveWorkflow = true;
        if (is_object($pa)) {
            $workflows = $pa->getWorkflows();
        }
        foreach ($workflows as $wf) {
            if (!$wf->canApproveWorkflow()) {
                $canApproveWorkflow = false;
            }
        }

        if (count($workflows) > 0 && !$canApproveWorkflow) {
            return true;
        }

        return false;
    }

    public function getPublishButtonActivated(Page $c): bool
    {
        if ($c->isPageDraft()) {
            $target = Page::getByID($c->getPageDraftTargetParentPageID());
            if (is_object($target) && !$target->isError() && $target->overrideTemplatePermissions()) {
                $c = $target;
            }
        }

        if ($this->canSubmitWorkflow($c)) {
            /** @var PageProgress[] $workflowList */
            $workflowList = PageProgress::getList($c);
            foreach ($workflowList as $wf) {
                $wfr = $wf->getWorkflowRequestObject();
                if ($wfr instanceof ApprovePageRequest) {
                    if ((int) $wfr->getRequestedVersionID() === (int) $c->getVersionID()) {
                        // If current version is already submitted to approve workflow, disable submit button
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getPublishButtonTitle(Page $c)
    {
        if ($c->isPageDraft()) {
            $publishTitle = t('Publish Page');
            $target = Page::getByID($c->getPageDraftTargetParentPageID());
            if (is_object($target) && !$target->isError() && $target->overrideTemplatePermissions()) {
                $c = $target;
            }
        } else {
            $publishTitle = t('Publish Changes');
        }

        if ($this->canSubmitWorkflow($c)) {
            $publishTitle = t('Submit to Workflow');
        }
        return $publishTitle;
    }

    public function getApproveButtonTitle(Page $c)
    {
        if ($this->canSubmitWorkflow($c)) {
            return t('Submit to Workflow');
        }

        return t('Approve');
    }

    public function displayPublishScheduleSettings(?Page $c = null)
    {
        View::element('pages/schedule', array(
            'page' => $c,
        ));
    }
}
