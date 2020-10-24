<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Workflow\Workflow;

defined('C5_EXECUTE') or die('Access Denied.');

class BasicWorkflow extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $accessDenied = true;
        $p = ConcretePage::getByPath('/dashboard/system/permissions/workflows');
        if ($p && !$p->isError()) {
            $cp = new Checker($p);
            if ($cp->canViewPage()) {
                $accessDenied = false;
            }
        }
        if ($accessDenied) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $workflowID = $options['wfID'] ?? null;
        $workflow = $workflowID ? Workflow::getByID($workflowID) : null;
        if ($workflow === null) {
            throw new UserMessageException(t('Workflow not found.'));
        }

        return $workflow;
    }
}
