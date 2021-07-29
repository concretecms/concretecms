<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Category\GenericTaskHandler;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

class Marketplace extends GenericTaskHandler
{
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
}
