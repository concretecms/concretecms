<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class PageType extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $accessDenied = true;
        $p = ConcretePage::getByPath('/dashboard/pages/types');
        if ($p && !$p->isError()) {
            $cp = new Checker($p);
            if ($cp->canViewPage()) {
                $accessDenied = false;
            }
        }
        if ($accessDenied) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pageTypeID = $options['ptID'] ?? null;
        $pageType = $pageTypeID ? Type::getByID($pageTypeID) : null;
        if (!$pageType) {
            throw new UserMessageException(t('Page type not found.'));
        }

        return $pageType;
    }
}
