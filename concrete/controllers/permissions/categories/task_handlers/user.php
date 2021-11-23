<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Category\GenericTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class User extends GenericTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\GenericTaskHandler::$hasWorkflows
     */
    protected $hasWorkflows = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\GenericTaskHandler::checkAccess()
     */
    protected function checkAccess(): void
    {
        $p = new Checker();
        if (!$p->canAccessTaskPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    protected function saveWorkflows(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->clearWorkflows();
        if (is_array($options['wfID'] ?? null)) {
            foreach ($options['wfID'] as $wfID) {
                $wf = Workflow::getByID($wfID);
                if ($wf !== null) {
                    $pk->attachWorkflow($wf);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
