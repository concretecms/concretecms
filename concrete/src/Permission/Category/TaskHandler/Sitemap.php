<?php

namespace Concrete\Core\Permission\Category\TaskHandler;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Category\DefaultTaskHandler;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

class Sitemap extends DefaultTaskHandler
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
}
