<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Category\DefaultTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class CalendarAdmin extends DefaultTaskHandler
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
        $wfIDs = $options['wfID'] ?? null;
        if (is_array($wfIDs)) {
            foreach ($wfIDs as $wfID) {
                $wf = Workflow::getByID($wfID);
                if ($wf !== null) {
                    $pa->attachWorkflow($wf);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
