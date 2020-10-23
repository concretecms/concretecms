<?php

namespace Concrete\Controller\Dialog\Workflow;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Workflow\Progress\PageProgress;
use Concrete\Core\Workflow\Request\ChangePagePermissionsRequest;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class ChangePagePermissions extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/workflow/change_page_permissions';

    public function view(int $wpID): ?Response
    {
        $workflowRequest = $this->getWorkflowRequest($wpID);
        $page = Page::getByID($workflowRequest->getRequestedPageID(), 'RECENT');
        if (!$page || $page->isError()) {
            throw new UserMessageException(t('Invalid workflow page'));
        }
        $this->set('workflowRequest', $workflowRequest);
        $this->set('page', $page);

        return null;
    }

    protected function getWorkflowRequest(int $wpID): ChangePagePermissionsRequest
    {
        $workflowProgress = PageProgress::getByID($wpID);
        if (!($workflowProgress instanceof PageProgress)) {
            throw new UserMessageException(t('Workflow progress not found'));
        }
        $workflow = $workflowProgress->getWorkflowObject();
        if (!$workflow->canApproveWorkflowProgressObject($workflowProgress)) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $workflowRequest = $workflowProgress->getWorkflowRequestObject();
        if (!$workflowRequest instanceof ChangePagePermissionsRequest) {
            throw new UserMessageException(t('Invalid workflow.'));
        }

        return $workflowRequest;
    }
}
