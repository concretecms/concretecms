<?php

namespace Concrete\Controller\Workflow\Categories;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Workflow\Progress\PageProgress;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Page extends AbstractController
{
    public function saveProgress(): HttpResponse
    {
        $this->checkCSRF('save_workflow_progress');
        $wp = $this->getWorkflowProgress();
        $task = $this->getWorkflowProgressTask($wp);
        if ($task === '') {
            throw new UserMessageException(t('Task not specified'));
        }
        $workflowResponse = $wp->runTask($task, $this->request->request->all());
        $responseData = [
            'wpID' => $wp->getWorkflowProgressID(),
            'redirect' => $workflowResponse instanceof WorkflowProgressResponse ? (string) $workflowResponse->getWorkflowProgressResponseURL() : '',
        ];
        if ($responseData['redirect'] === '') {
            $responseData['redirect'] = (string) $this->app->make(ResolverManagerInterface::class)->resolve([$this->getPage()]);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($responseData);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(string $action): void
    {
        $valt = $this->app->make(Token::class);
        if (!$valt->validate($action)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getWorkflowProgress(): PageProgress
    {
        $wpID = $this->request->request->get('wpID', $this->request->query->get('wpID'));
        $wp = $this->app->make(Numbers::class)->integer($wpID, 1) ? PageProgress::getByID((int) $wpID) : null;
        if (!($wp instanceof PageProgress)) {
            throw new UserMessageException(t('Workflow progress not found'));
        }
        $wf = $wp->getWorkflowObject();
        if (!$wf->canApproveWorkflowProgressObject($wp)) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $wp;
    }

    protected function getWorkflowProgressTask(PageProgress $wp): string
    {
        return (string) $wp->getRequestedTask();
    }

    protected function getPage(): ?ConcretePage
    {
        $pageID = $this->request->request->get('cID', $this->request->query->get('cID'));
        $page = $this->app->make(Numbers::class)->integer($pageID, 1) ? ConcretePage::getByID((int) $pageID) : null;
        if ($page instanceof ConcretePage && !$page->isError()) {
            return $page;
        }

        return null;
    }
}
