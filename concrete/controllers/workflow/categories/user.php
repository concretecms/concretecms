<?php

namespace Concrete\Controller\Workflow\Categories;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Workflow\Progress\Category;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Progress\UserProgress;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class User extends AbstractController
{
    public function saveProgress(): HttpResponse
    {
        $this->checkCSRF('save_user_workflow_progress');
        $wp = $this->getWorkflowProgress();
        $task = (string) $wp->getRequestedTask();
        if ($task === '') {
            throw new UserMessageException(t('Task not specified'));
        }
        $workflowResponse = $wp->runTask($task, $this->request->request->all());
        $responseData = [
            'wpID' => $wp->getWorkflowProgressID(),
            'redirect' => $workflowResponse instanceof WorkflowProgressResponse ? (string) $workflowResponse->getWorkflowProgressResponseURL() : '',
        ];
        if ($responseData['redirect'] !== '') {
            $responseData['message'] = $workflowResponse->message;
        } else {
            $url = $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/users/search']);
            $query = $url->getQuery();
            $query->modify(['uID' => (int) $this->request->request->get('uID', $this->request->query->get('uID'))]);
            $responseData['redirect'] = (string) $url->setQuery($query);
        }
        $responseData['tableData'] = $this->renderTableData($wp);

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
    protected function getWorkflowProgress(): UserProgress
    {
        $wpID = $this->request->request->get('wpID', $this->request->query->get('wpID'));
        $wp = $this->app->make(Numbers::class)->integer($wpID, 1) ? UserProgress::getByID((int) $wpID) : null;
        if (!($wp instanceof UserProgress)) {
            throw new UserMessageException(t('Workflow progress not found'));
        }
        $wf = $wp->getWorkflowObject();
        if (!$wf->canApproveWorkflowProgressObject($wp)) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $wp;
    }

    protected function renderTableData(UserProgress $wp): string
    {
        $category = Category::getByID($wp->getWorkflowProgressCategoryID());
        $list = $category->getPendingWorkflowProgressList();
        $items = $list->get();
        ob_start();
        try {
            View::element('workflow/progress/categories/user/table_data', ['items' => $items, 'list' => $list]);

            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
