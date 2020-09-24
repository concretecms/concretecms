<?php

namespace Concrete\Core\Permission\Category\TaskHandler;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Category\DefaultTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class User extends DefaultTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\DefaultTaskHandler::checkAccess()
     */
    protected function checkAccess(): void
    {
        $p = new Checker();
        if (!$p->canAccessTaskPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\DefaultTaskHandler::savePermission()
     */
    protected function savePermission(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);
        $pa->clearWorkflows();
        if (is_array($options['wfID'] ?? null)) {
            foreach ($options['wfID'] as $wfID) {
                $wf = Workflow::getByID($wfID);
                if (is_object($wf)) {
                    $pa->attachWorkflow($wf);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
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
