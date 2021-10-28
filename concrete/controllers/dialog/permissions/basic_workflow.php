<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Workflow\Workflow;

defined('C5_EXECUTE') or die('Access Denied.');

class BasicWorkflow extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/basic_workflow';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $p = Page::getByPath('/dashboard/system/permissions/workflows');
        $cp = new Checker($p);

        return $cp->canViewPage();
    }

    public function view()
    {
        $this->set('workflow', $this->getWorkflow());
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getWorkflow(): Workflow
    {
        $workflowID = $this->request->request->get('wfID', $this->request->query->get('wfID'));
        $workflowID = $this->app->make(Numbers::class)->integer($workflowID, 1) ? (int) $workflowID : null;
        $workflow = $workflowID === null ? null : Workflow::getByID($workflowID);
        if ($workflow === null) {
            throw new UserMessageException(t('Workflow not found'));
        }

        return $workflow;
    }
}
